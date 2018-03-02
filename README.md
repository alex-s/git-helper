# git-helper
Adds few useful features for manipulation of local git repository
* git:find <search>
* git:review <search>
## installation

```
> git clone git@github.com:alex-s/git-helper.git && cd git-helper
> composer install
> sudo chmod + x app
> sudo ln -s $(pwd)/app /usr/local/bin/YOUR_ALIAS
```  

## usage

```
(any git folder )> YOUR_ALIAS git:find 1234
# find branch by pattern and checkout to it
```
```
(any git folder )> YOUR_ALIAS git:review 1234
# this is equivalent of 
# > git checkout master
# > git branch -D 1234
# > git checkout 1234

```  