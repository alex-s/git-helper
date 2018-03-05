# git-helper
Adds few useful features for manipulation of local git repository
* find <search>
* review <search>
* create <issue_num>
## installation

```
> git clone git@github.com:alex-s/git-helper.git && cd git-helper
> composer install
> sudo chmod + x app
> sudo ln -s $(pwd)/app /usr/local/bin/YOUR_ALIAS
```  

## usage

```
(any git folder )> YOUR_ALIAS create 1234
# search issue in jira (need fill params.ini) 
# and create branch according pattern
# the same as
# > git checkout -d your_issue_pattern_name
```
```
(any git folder )> YOUR_ALIAS find 1234
# find branch by pattern and checkout to it
```
```
(any git folder )> YOUR_ALIAS review 1234
# this is equivalent of 
# > git checkout master
# > git branch -D 1234
# > git checkout 1234

```  