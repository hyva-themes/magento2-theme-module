#!/usr/bin/env bash
for FILE in src/view/frontend/web/svg/heroicons/outline/*.svg
do
    echo -n " * @method string "
    echo -n $(basename -s .svg $FILE | sed -E 's/-(.)/\U\1/g')
    echo "Html(string \$classnames = '', ?int \$width = 24, ?int \$height = 24, array \$attributes = [])"
done
