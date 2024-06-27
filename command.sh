// import translation file to ui site configuration
vendor/bin/drush locale:import ar modules/custom/custom_module/translations/ar.po

// drush command to generate custom module 
drush gen module-standard --directory modules/custom --answers '{"name": "module name"}'

// generate secret key 64base
openssl rand -hex 64

// lando comands
lando start
lando abracadabra
lando poweroff
lando drush uli

// git configuration
git config --global user.name "FIRST_NAME LAST_NAME"
git config --global user.email "MY_NAME@example.com"

// Create an empty Git repository or reinitialize an existing one
git init

// Clone a repository into a new directory
git clone https://github.com/YOUR-USERNAME/YOUR-REPOSITORY

// Create a new branch 
git branch BRANCH_NAME
or
git checkout -b BRANCH_NAME

// Change to another branch
git checkout BRANCH_NAME

// Delete branch
git branch -D BRANCH_NAME

// Add file contents to the index
git add FILE_NAME

// OR adding all content by 
git add .

// Record changes to the repository only title
git commit -m "MESSAGE"

// Record changes to the repository title with body
git commit

// Show the working tree status
git status 

// Fetch from and integrate with another repository or a local branch
git pull origin BRANCH_NAME

// Push all files to the repository
git push origin BRANCH_NAME

// Clean all files
git clean -fd

// Make your Terminal Faster
To summarize, open up a Terminal window and type the following command:
=> defaults write NSGlobalDomain KeyRepeat -int 0
Then restart your Device .