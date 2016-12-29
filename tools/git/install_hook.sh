#!/bin/sh
echo "Installing pre commit hook."
cp tools/git/pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
