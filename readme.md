# Haunt (aplha)

Note that the implementation is currently being ported. 

Haunt is a php equivalent for wraith to prevent having too many different
dependencies on your testing servers. It's main purpose is to provide 
a very low effort way of checking functionallity and layout for 
continuous integration projects. 

## Key concepts
The main idea is to keep the various components of the tool as autonomous
as possible. So it can support as many sources as possible. The basic 
command (compare) allows you to compare any sets of files on your filesystem
regardless of the source. 

### Providing screenshots
WIP, currently no basic implementation exists. (but one will be added). 

### Comparing screenshots
To compare screenshots the following dir structure is required. 

``` 
    WHEREVER/group-name
                /screenshot-set-name
                    - current.png
                    - baseline.png
```

Afterwards you can run haunt and a new file "diff.png" will be added. 

``` 
    ./vendor/bin/haunt compare --source=WHEREVER
```