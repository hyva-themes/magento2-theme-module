#!/bin/bash

# Define the directory containing the SVG icons
directory="src/view/frontend/web/svg/heroicons"

# Function to modify SVG files recursively
function modify_svg_files() {
  for file in "$directory"/**/*.svg; do
    # Skip directories (already handled by globstar syntax) and hidden files
    [[ -d "$file" || "$file" =~ ^\.*$ ]] && continue

    # Check if the file contains the target string
    if grep -q 'stroke="#111827"' "$file"; then
      # Read file content
      content=$(cat "$file")

      # Replace target string only (without any additional characters)
      new_content="${content//stroke=\"#111827\" /}"

      # Write modified content back to the file
      echo "$new_content" > "$file"
    fi
  done
}

# Execute the function
modify_svg_files
