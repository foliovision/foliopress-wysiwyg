#!/usr/bin/bash
# This script performs some operations to make KFM release ready

# Remove .svn directories
find . -name .svn -exec rm -rf {} \;

# Remove some plugins
for plugin in codepress return_thumbnail
do
	rm -rf plugins/$plugin
done

# Remove some jquery files
rm -f j/jquery/jquery.impromptu.js

# clean up unnecessary third-party files (keep things clean!)
rm -rf third-party/swfupload/Documentation
rm -f "third-party/swfupload/Core Changelog.txt"

# minify all scripts (requires Douglas Crockford's JSMin - http://javascript.crockford.com/jsmin.html)
find . -name '*.js' -exec sh -c 'jsmin < {} > {}.new && mv {}.new {}' \;

# And last but not least, remove the release scripts
rm -f release.sh release.rb
