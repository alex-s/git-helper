# git-helper
Adds few useful features for manipulation of local git repository
* find <search>
* review <search>
* pull
* push
## installation

```
> git clone git@github.com:alex-s/git-helper.git && cd git-helper
> composer install
> sudo chmod + x app
> sudo ln -s $(pwd)/app /usr/local/bin/YOUR_ALIAS
```  

## usage

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
```
(any git folder )> YOUR_ALIAS pull
# pull data from origin current branch
```
```
(any git folder )> YOUR_ALIAS push
# push data to origin current branch
```