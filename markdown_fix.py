#!/usr/bin/env python3
"""
Markdown linting fix script for SDD document
Fixes common markdown linting issues like blanks around headings and lists
"""

import re

def fix_markdown_formatting(content):
    lines = content.split('\n')
    fixed_lines = []
    
    for i, line in enumerate(lines):
        # Add blank line before headings (except first line)
        if line.startswith('#') and i > 0 and lines[i-1].strip() != '':
            fixed_lines.append('')
        
        fixed_lines.append(line)
        
        # Add blank line after headings if next line isn't blank
        if line.startswith('#') and i < len(lines) - 1 and lines[i+1].strip() != '':
            if not lines[i+1].startswith('#'):  # Don't add if next line is also a heading
                fixed_lines.append('')
        
        # Add blank lines around bullet lists
        if i < len(lines) - 1:
            current_is_list = line.strip().startswith('- ')
            next_is_list = lines[i+1].strip().startswith('- ')
            next_line_exists = i+1 < len(lines)
            
            # Add blank line after list ends
            if current_is_list and next_line_exists and not next_is_list and lines[i+1].strip() != '':
                fixed_lines.append('')
            
            # Add blank line before list starts  
            if not current_is_list and next_is_list and line.strip() != '':
                if not line.startswith('#'):  # Don't add if current line is heading
                    fixed_lines.append('')
    
    return '\n'.join(fixed_lines)

def fix_fenced_code_blocks(content):
    # Add blank lines around fenced code blocks
    lines = content.split('\n')
    fixed_lines = []
    
    in_code_block = False
    for i, line in enumerate(lines):
        if line.strip().startswith('```'):
            if not in_code_block:  # Starting code block
                if i > 0 and lines[i-1].strip() != '':
                    fixed_lines.append('')
                fixed_lines.append(line)
                in_code_block = True
            else:  # Ending code block
                fixed_lines.append(line)
                if i < len(lines) - 1 and lines[i+1].strip() != '':
                    fixed_lines.append('')
                in_code_block = False
        else:
            fixed_lines.append(line)
    
    return '\n'.join(fixed_lines)

# Read the file
with open('/home/winzer/Desktop/code/class/Aktina/info/sdd1.md', 'r') as f:
    content = f.read()

# Apply fixes
content = fix_markdown_formatting(content)
content = fix_fenced_code_blocks(content)

# Remove multiple consecutive blank lines
content = re.sub(r'\n{3,}', '\n\n', content)

# Write back to file
with open('/home/winzer/Desktop/code/class/Aktina/info/sdd1.md', 'w') as f:
    f.write(content)

print("Markdown formatting fixes applied successfully!")
