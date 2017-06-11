# postbit-dice
Postbit interface for triggering simple randomized dice rolls.

### Pre-Installation
postbit-dice makes use of a hardcoded user with `uid` 999999999. If this user id isn't available, installation will fail or cause unintended behavior.  
The user and post creation makes SQL queries to the builtin _posts and _users tables. If other plugins make additional fields to these tables without well defined defaults, postbit-dice may create 'broken' posts/user accounts/tables.  
Attempt installation with existing plugins in a test environment beforehand.  

### Installation
Copy the files into the root of the MyBB installation. This should merge the contents of the inc/plugin folder with the existing one.  
The plugin should appear in the MyBB plugin manager in the Admin CP as 'Postbit Dice'. Select the 'Install & Activate' option.  

### Usage
After installation, each post will have a 'Roll Dice' option to the left of 'Edit' button. The button will trigger a pop-over window similar to the default report button with five fields:  
- `tid` => Thread id to post dice rolls to.  
- `pid` => Post id to reply to.  
- `# of rolls` => Number of times to roll the dice.  
- `Lower bound`/`Upper bound` => INCLUSIVE Range of values to return on the dice roll.  
On submission of valid data, the thread will be replied to with the resulting random values.  
Currently, only users with admin permissions can see the results and trigger dice rolls.

### Changelog
###### 0.0.1
- Base ungeneralized functionality.

### Todo 
- Automate testing
- MyBB settings and plugins
    - Usergroup selection to trigger rolls
    - Usergroup selection to who can see rolls
    - Specify user account for rolls
    - Activation/deactivation functions
- MyBB templating
    - Add theme inclusion to dice form 
    - Refactor/Move html and js strings into MyBB templates
- Generalize SQL queries
- Generalize
- Add user triggering rolls to returned posts

### Known Issues
- Need userid 999999999 for installation, make selection dynamic.
