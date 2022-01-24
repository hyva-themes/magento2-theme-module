#!/usr/bin/env bash

if [ "$(echo x | sed -E 's/(.)/\U\1/')" != 'X' ]; then
    # This script is incompatible with sed on Mac OS or BSD, as they do not support \U in extended regular expressions.
    echo "Incompatible version of sed found: $(which sed)" >&2
    echo "GNU sed with full extended regex support is required" >&2
    exit 1
fi

for FILE in src/view/frontend/web/svg/heroicons/outline/*.svg
do
    echo -n " * @method string "
    echo -n $(basename -s .svg $FILE | sed -E 's/-(.)/\U\1/g')
    echo "Html(string \$classnames = '', ?int \$width = 24, ?int \$height = 24, array \$attributes = [])"
done
