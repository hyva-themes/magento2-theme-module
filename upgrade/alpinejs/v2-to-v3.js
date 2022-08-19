#!/usr/bin/env node

const {basename, join} = require('path');
const fs = require('fs');

// --------------------------- test functions ---------------------------

/**
 * @param sut {{Function: String}}
 * @param {Array<{input: string, expected: string}>} cases
 */
function runTests(suite, sut, cases) {
    const fails = [], errors = [];
    for (const n in cases) {
        try {
            const testcase = cases[n];
            const actual = sut(testcase.input)
            const result = testcase.assert ? testcase.assert(actual) : true;

            if (result !== true || testcase.expected !== undefined && actual !== testcase.expected) {
                fails.push(n);
                console.log(`${suite}: TestCase ${parseInt(n) + 1} failed:`);
                if (result !== true) {
                    console.log(` ${result}`);
                    console.log(` ACTUAL: `, actual);
                } else {
                    console.log(` EXPECTED: "${testcase.expected}"`);
                    console.log(` ACTUAL:   "${actual}"`);
                }
            }
        } catch (e) {
            errors.push(n)
            console.log(`${suite}: TestCase ${parseInt(n) + 1} threw an exception:`);
            console.log(` ERROR: ${n}/${cases.length}:`);
            console.log(e.message || e);
        }
    }

    if (fails.length > 0) {
        console.log(`${suite}: FAILED ${fails.length} out of ${cases.length}`);
    }

    if (errors.length > 0) {
        console.log(`${suite}: ERROR ${errors.length} out of ${cases.length}`);
    }

    console.log(`${suite}: PASSED ${cases.length - errors.length - fails.length} out of ${cases.length} `);

    return fails.length === 0 && errors.length === 0;
}

const assertArrayIsEmpty = actual => Array.isArray(actual) && actual.length === 0 || `EXPECTED: []`;

const assertFirstMessageIs = (expected) => actual => {
    const msg = Array.isArray(actual) && actual.length > 0 ? actual[0] : '';
    return msg === expected || `EXPECTED: ${expected}`;
}

const isTestRun = process.argv.includes(`--test`)

let testsPassed = true;

// --------------------------- migration functions ---------------------------

function check(content) {
    return [
        content => content.match(/x-if\.transition/) && 'x-if.transition is no longer supported in Alpine v3. A manual update to use x-show is required.',
        content => content.match(/deferLoadingAlpine/) && 'Global lifecycle events have to be used instead of Alpine.deferLoadingAlpine() in v3. A manual update to use alpine:init and alpine:initialized is required.',
        content => content.match(/:x-ref/) && 'x-ref no longer supports binding in Alpine v3. A manual update to use statix x-ref is required.',
        // Not applicable to Hyvä, since we encourage global functions because they provide extensibility via wrapper proxies
        // content => {
        //     const m = content.match(/x-data="([a-z0-9_.]+\([^)]*\))"/i);
        //     return m && `Recommendation: refactor global function data provider x-data="${m[1]}" use Alpine.data() instead.`
        // }
        content => content.match(/:class="[^"]*?hidden'?\s*:[^"]*?"/) && content.match(/click.away/) && `Check complex refactoring: of \`@click.away="condition=false"\` together with \`:class="{'hidden':condition}"\` to \`x-show\``
    ].reduce((acc, fn) => {
        const result = fn(content);
        if (result) acc.push(result);
        return acc;
    }, []);
}

// check test cases
isTestRun && (testsPassed = runTests('check', check, [
    {
        input: `<template x-if.transition="true">...</template>`,
        assert: assertFirstMessageIs(`x-if.transition is no longer supported in Alpine v3. A manual update to use x-show is required.`)
    },
    {
        input: `<template x-if="true">...</template>`,
        assert: assertArrayIsEmpty
    },
    {
        input: `window.deferLoadingAlpine = startAlpine => { startAlpine() }`,
        assert: assertFirstMessageIs('Global lifecycle events have to be used instead of Alpine.deferLoadingAlpine() in v3. A manual update to use alpine:init and alpine:initialized is required.')
    },
    {
        input: `<div :x-ref="option.value" x-text="option.value"></div>`,
        assert: assertFirstMessageIs(`x-ref no longer supports binding in Alpine v3. A manual update to use statix x-ref is required.`)
    },
    {
        input: `<div x-bind:x-ref="attributes('foo')"></div>`,
        assert: assertFirstMessageIs(`x-ref no longer supports binding in Alpine v3. A manual update to use statix x-ref is required.`)
    },
    {
        input: `<div x-ref="attributes('foo')"></div>`,
        assert: assertArrayIsEmpty
    },
    // Not applicable to Hyvä, since we encourage global functions because they provide extensibility via wrapper proxies
    // {
    //     input: `<div x-data="initSomeComponent({})" class="container">...</div>`,
    //     assert: assertFirstMessageIs(`Recommendation: refactor global function data provider x-data="initSomeComponent({})" use Alpine.data() instead.`)
    // },
    // {
    //     input: `<div x-data="initSomeComponent" class="container">...</div>`,
    //     assert: assertArrayIsEmpty
    // },
    {
        input: `<div x-data="" class="container">...</div>`,
        assert: assertArrayIsEmpty
    },
    {
        input: `<div x-data="() => { ... }" class="container">...</div>`,
        assert: assertArrayIsEmpty
    },
    {
        input: `<div class="absolute hidden w-full" :class="{ 'hidden' : !show }" @click.away="show = false">`,
        assert: assertFirstMessageIs(`Check complex refactoring: of \`@click.away="condition=false"\` together with \`:class="{'hidden':condition}"\` to \`x-show\``)
    },
    {
        input: `<div class="block-content filter-content pt-3 hidden md:block" :class="{ 'hidden' : isMobile && !blockOpen }">`,
        assert: assertArrayIsEmpty
    },
]) && testsPassed);


function transform(content) {
    return content
        .replace(/\$el(?![a-z0-9_])/g, '$root')
        .replace(/\s*x-init="init(?:\(\))?"/g, '')
        .replace(/x-show\.transition([^=]*)="/g, (match, params) => {
            const transitionAttrs = params.split('.').reduce((acc, part) => {
                if (part.length === 0) return acc;
                if (part === 'in') return acc + (acc.length ? ' ' : '') + 'x-transition:enter';
                if (part === 'out') return acc + (acc.length ? ' ' : '') + 'x-transition:leave';
                return acc + (acc.length === 0 ? 'x-transition.' : '.') + part;
            }, '');
            return `${transitionAttrs.length ? transitionAttrs : 'x-transition'} x-show="`;
        })
        .replace(/x-init="(\([^"]+)"/gs, (match, callback) => `x-init="$nextTick(${callback})"`)
        .replace(/x-spread=/g, 'x-bind=')
        .replace(/(x-on:|@)([^=]*).away(.[^[=]+)?=/gs, (match, prefix, before, after) => `${prefix}${before}.outside${after}=`)
        // Don't use `class="hidden" @click.outside="condition=false"` together with `:class="{'hidden': condition}"`
        // Instead use `class="" x-cloak x-show="!condition"` (no change to the `@click.outside` handler)
        .replace(/(?<start><[^>]*?)class="(?<classBefore>[^"]*)(?<=["\s])hidden(?=["\s])(?<classAfter>[^"]*)"(?<otherAttrs>.*?)(?<bindPrefix>x-bind:|:)class="(?<expressionBefore>[^}]*?)'?hidden'?\s*:(?<expression>[^,}]+)(?<expressionAfter>[^"]+?)"(?<end>[^>]*?>)/gmis, (...p) => {
            const {start, classBefore, classAfter, otherAttrs, bindPrefix, expressionBefore, expression, expressionAfter, end} = p[p.length -1];
            if (! p[0].match(/click.outside/)) return p[0];
            let bind = ''
            if (expressionBefore.match(/[^{\s]+/) || expressionAfter.match(/[^}\s]+/)) {
                const before = expressionBefore.replace(/[,\s]+$/, '')
                const after = expressionAfter.replace(/^[,\s]+/, '')
                bind = `${bindPrefix}class="${before}${before.length > 1 && after.length > 1 ? ', ' : ''}${expressionAfter.replace(/^[,\s]+/, '')}" `
            }
            const classBetween = classBefore.trim().length > 0 && classAfter.trim().length > 0 ? ' ' : '';
            const inverseExpression = expression.trim().match(/^!?\s*[a-z]+\s*$/i) // simple case like `! open` or `show`
                ? ('!' + expression.trim()).replace('!!', '').trim() // simple case, avoid ugly double exclamation mark !!
                : `!(${expression})` // wrap complex expressions in parens
            return `${start.replace(/x-show="[^"]+"\s*/, '')}class="${classBefore.trim()}${classBetween}${classAfter.trim()}"${otherAttrs.replace(/\s*x-show="[^"]+"\s*/, '')}${bind}x-cloak x-show="${inverseExpression}"${end.replace(/\s*x-show="[^"]+"/, '')}`
        })
}

// transform test cases
isTestRun && (testsPassed = runTests('transform', transform, [
    {
        input: `<img class="hover:shadow-sm object-contain" x-data="" @update-gallery-5.window="$el.src = $event.detail" src="//media/foo.png" loading="lazy" width="100" height="100" />`,
        expected: `<img class="hover:shadow-sm object-contain" x-data="" @update-gallery-5.window="$root.src = $event.detail" src="//media/foo.png" loading="lazy" width="100" height="100" />`
    },
    {
        input: `<img class="hover:shadow-sm object-contain" x-data="" @update-gallery-5.window="$ele.src = $event.detail" src="//media/foo.png" loading="lazy" width="100" height="100" />`,
        expected: `<img class="hover:shadow-sm object-contain" x-data="" @update-gallery-5.window="$ele.src = $event.detail" src="//media/foo.png" loading="lazy" width="100" height="100" />`
    },
    {
        input: `<div x-data="" x-init="init()">...</div>`,
        expected: `<div x-data="">...</div>`
    },
    {
        input: `<div x-data="" x-init="init">...</div>`,
        expected: `<div x-data="">...</div>`
    },
    {
        input: `<div x-data=""
                x-init="init">...</div>`,
        expected: `<div x-data="">...</div>`
    },
    {
        input: `<div x-data="" x-init="() => { ... }">...</div>`,
        expected: `<div x-data="" x-init="$nextTick(() => { ... })">...</div>`
    },
    {
        input: `<div x-show.transition="open"></div>`,
        expected: `<div x-transition x-show="open"></div>`
    },
    {
        input: `<div x-show.transition.duration.500ms="open"></div>`,
        expected: `<div x-transition.duration.500ms x-show="open"></div>`
    },
    {
        input: `<div x-show.transition.in.duration.500ms.out.duration.750ms="open"></div>`,
        expected: `<div x-transition:enter.duration.500ms x-transition:leave.duration.750ms x-show="open"></div>`
    },
    {
        input: `<div x-data="dropdown()">
    <button x-spread="trigger">Toggle</button>
    <div x-spread="dialogue">...</div>
    </div>`,
        expected: `<div x-data="dropdown()">
    <button x-bind="trigger">Toggle</button>
    <div x-bind="dialogue">...</div>
    </div>`
    },
    {
        input: `<div x-bind:class="stuff()"> </div>`,
        expected: `<div x-bind:class="stuff()"> </div>`
    },
    {
        input: `<div x-on:click.away="show = false"> </div>`,
        expected: `<div x-on:click.outside="show = false"> </div>`
    },
    {
        input: `<div @click.away="show = false"> </div>`,
        expected: `<div @click.outside="show = false"> </div>`
    },
    {
        input: `<div @click.away.once="show = false"> </div>`,
        expected: `<div @click.outside.once="show = false"> </div>`
    },
    {
        input: `<div @click.once.away="show = false"> </div>`,
        expected: `<div @click.once.outside="show = false"> </div>`
    },
    {
        input: `<div @click.once.away.window="show = false"> </div>`,
        expected: `<div @click.once.outside.window="show = false"> </div>`
    },
    {
        input: `<div class="absolute hidden w-full" :class="{ 'hidden' : !show }" @click.outside="show = false">`,
        expected: `<div class="absolute w-full" x-cloak x-show="show" @click.outside="show = false">`
    },
    {
        input: `<div class="absolute hidden" :class="{hidden: !open}" @click.outside="open = false">`,
        expected: `<div class="absolute" x-cloak x-show="open" @click.outside="open = false">`
    },
    {
        input: `<div class="hidden w-full" :class="{hidden: hide}" @click.outside="hide = true">`,
        expected: `<div class="w-full" x-cloak x-show="!hide" @click.outside="hide = true">`
    },
    {
        input: `<div class="absolute hidden w-full" :class="{wFull: bar, hidden: hide}" @click.outside="hide = true">`,
        expected: `<div class="absolute w-full" :class="{wFull: bar}" x-cloak x-show="!hide" @click.outside="hide = true">`
    },
    {
        input: `<div class="absolute hidden w-full" :class="{hidden: hide, 'my-block': isBlock}" @click.outside="hide = true">`,
        expected: `<div class="absolute w-full" :class="{'my-block': isBlock}" x-cloak x-show="!hide" @click.outside="hide = true">`
    },
    {
        input: `<div class="absolute hidden w-full" :class="{wFull: bar, hidden: hide, 'my-block': isBlock}" @click.outside="hide = true">`,
        expected: `<div class="absolute w-full" :class="{wFull: bar, 'my-block': isBlock}" x-cloak x-show="!hide" @click.outside="hide = true">`
    },
    {
        input: `<div class="absolute z-10 hidden w-full border-t shadow-sm bg-container-lighter border-container-lighter"
         id="search-content"
         :class="{ 'block': searchOpen, 'hidden': !searchOpen }"
         @click.outside="searchOpen = false"
         x-show="true"
    >`,
        expected: `<div class="absolute z-10 w-full border-t shadow-sm bg-container-lighter border-container-lighter"
         id="search-content"
         :class="{ 'block': searchOpen}" x-cloak x-show="searchOpen"
         @click.outside="searchOpen = false"
    >`
    },
    {
        input: `<nav class="absolute right-0 z-20 hidden w-40 py-2 mt-2 -mr-4 px-1 overflow-auto origin-top-right rounded-sm
        shadow-lg sm:w-48 lg:mt-3 bg-container-lighter"
         :class="{ 'hidden' : !open }"
         @click.outside="open = false"
         aria-labelledby="customer-menu"
    >`,
        expected: `<nav class="absolute right-0 z-20 w-40 py-2 mt-2 -mr-4 px-1 overflow-auto origin-top-right rounded-sm
        shadow-lg sm:w-48 lg:mt-3 bg-container-lighter"
         x-cloak x-show="open"
         @click.outside="open = false"
         aria-labelledby="customer-menu"
    >`
    },
    {
        // check ignores class binding 'hidden' without click.outside listener
        input: `<div class="block-content filter-content pt-3 hidden md:block" :class="{ 'hidden' : isMobile && !blockOpen }">`,
        expected: `<div class="block-content filter-content pt-3 hidden md:block" :class="{ 'hidden' : isMobile && !blockOpen }">`
    },
]) && testsPassed)

// --------------------------- filesystem functions ---------------------------

function processFile(filepath) {
    if (!fs.existsSync(filepath)) return;
    const origContent = fs.readFileSync(filepath, {encoding: 'utf8'});
    const warnings = check(origContent);
    if (warnings.length) {
        console.log(`  File: ${filepath}`);
        warnings.forEach(msg => console.log(`    ${msg}`));
    }
    const transformedContent = transform(origContent);
    if (transformedContent !== origContent) {
        fs.writeFileSync(`${filepath}~`, transformedContent);
        fs.renameSync(`${filepath}~`, filepath);
    }
}

function processDir(dirpath) {
    fs.readdir(dirpath, (err, items) => {
        if (err) throw err;
        items.map(item => join(dirpath, item))
            .filter(item => isDir(item) || item.endsWith('.phtml'))
            .forEach(processItem);
    })
}

function isDir(filepath) {
    return fs.existsSync(filepath) && fs.statSync(filepath).isDirectory(filepath)
}

function processItem(filepath) {
    return isDir(filepath) ? processDir(filepath) : processFile(filepath);
}

// --------------------------- process arguments ---------------------------

if (process.argv.length === 2 || process.argv.includes('--help') || process.argv.includes('-h') || process.argv.includes('help')) {
    const cwd = process.cwd();
    const script = __filename.slice(0, cwd.length) === cwd ? __filename.slice(cwd.length + 1) : basename(__filename);
    console.log(`
This script attempts to upgrade Alpine.js v2 code to v3 as described at https://alpinejs.dev/upgrade-guide

Usage:
   ${script} dir1 [dirs...]    Recurse directories and process all .phtml files
   ${script} file1 [files...]  Process the specified files
   ${script} --test            Run unit tests (for development purposes only)

NOTE: ----> The script assumes you keep all theme files in git, no backups are made! <----
`);
    process.exit(1);
}

if (isTestRun) {
    process.exit(testsPassed ? 0 : 1);
}

// --------------------------- process files ---------------------------

process.argv.slice(2).filter(i => i !== '--test').forEach(processItem);
