# hidden-replies
Adds options to allow moderators to create hidden replies to threads.  
Currently a WIP.

### Pre-Installation


### Installation
Copy the files into the root of the MyBB installation. This should merge the contents of the inc/plugin folder with the existing one.  
The plugin should appear in the MyBB plugin manager in the Admin CP as 'Hidden Replies'. Select the 'Install & Activate' option.  

### Usage
Replies have a checkbox for "Hide Post" in the moderators options.

### Changelog
###### 0.0.1
- Removed non-functional hide post option for normal users (Moved template to piggyback off close_thread)
###### 0.0.0
- Base functionality.


### Todo 

### Known Issues
- Ajax requests complete but sometimes hang on refreshing with invalid json return

