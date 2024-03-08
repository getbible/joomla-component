# v3.0.6

- Correct the url encoding to json_encode for none Latin languages.
- Fix type cast validation on search page.

# v3.0.5

- Fix missing token variable in ajax call 

# v3.0.4

- Fix an Ajax input typo.

# v3.0.3

- Add option to target mysql 8+ with the search regex.

# v3.0.2

- More namespace updates.

# v3.0.1

- Fix missing use statement in app model.

# v3.0.0

- Move all JText to use the namespaced class Text directly.
- Move all JHtml to use the namespaced class Html directly.
- Move all JFactory to use the namespaced class Factory directly.
- Move all JRoute to use the namespaced class Route directly.
- Move all JFormHelper to use the namespaced class FormHelper directly.
- Move all JLayout to use the namespaced class FileLayout directly.
- Move all JLanguageMultilang to use the namespaced class Multilanguage directly.
- Move all JComponentHelper to use the namespaced class ComponentHelper directly.
- Move all JCategoryNode to use the namespaced class CategoryNode directly.
- Move all JComponentHelper to use the namespaced class ComponentHelper directly.
- Move all JToolbar to use the namespaced class Toolbar directly.
- Move all JToolbarHelper to use the namespaced class ToolbarHelper directly.
- Convert all addStyleSheet to make use of Html class instead.
- Convert all addScript to make use of Html class instead.

# v2.0.32

- Fixed other search related issues.

# v2.0.31

- Fixed #10 so that exact search results now work correctly.
- Update scripture loader to version 3.0.3

# v2.0.30

- Update scripture loader to version 3.0.2

# v2.0.29

- Update scripture loader to version 3.0.1

# v2.0.28

- Small xml fix

# v2.0.27

- Improved the load scripture plugin.

# v2.0.26

- Adds few try catch blocks in the API.
- Adds local link to daily scripture module.

# v2.0.25

- Adds getBible Loader Plugin

# v2.0.23

- Refactored all core helper functions to make use of New classes
- Adds open ai meta data to page
- Other JCB fixes

# v2.0.22

- Fixed search redirect bug

# v2.0.21

- Adds Tags meta data to tag pages
- Adds option to share a tag
- Improve the URL creation, and return URL feature for search and tag pages

# v2.0.20

- First step to resolve getBible/support#8 so that the selection works on mobile browsers.
- Fixed the scrolling for mobiles.

# v2.0.19

- Adds metadata to each Bible page to resolve getBible/support#6
- Adds option to force chapter hash checking.

# v2.0.18

- Adds bottom module position on tag, search, ai and app pages.
- Fixed JavaScript Database Manager some more.

# v2.0.17

- Adds brut-force protection

# v2.0.16

- Fixed JavaScript Database Manager

# v2.0.15

- Adds new session option
- Adds make public switches to back-end

# v2.0.14

- Adds install mysql commands for faster queries on large systems.
- Fixes mobile layout on settings active session tab.
- Making correction to tag descriptions.

# v2.0.13

- Fix tag issues
- Adds Footable to back-end
- Fix chapter issue of app page
- Moves translations tab

# v2.0.12

- Fixes Links to Translations (to use their own book names)

# v2.0.11

- Adds better translation selection by Language

# v2.0.9

- Adds create tags on front-end.
- Adds update tags on front-end.
- Adds delete of tags on front-end.
- Improves verse view in note, and tag modal.
- Other bug fixes.

# v2.0.8

- Adds easy option to update book names in the back-end.
- Adds easy option to sync translations details in the back-end.

# v2.0.7

- Adds force update option
- Improves the book name display on Bible page

# v2.0.6

- Adds updating watchers for book names, and translation details.
- Adds edit option to owned tags
- Better session management that allows sharing sessions.
- Few bug fixes

# v2.0.5

- Adds list of default system tags
- Adds linker session manager
- Adds option to share sessions

# v2.0.4

- Added the option to set the default Translation.
- Fixed sharing of a verse, so its auto selected when verse number is clicked.

# v2.0.3

- Fixed getBible/support#2 so that the view value does not result into Undefined.
- Fixed getBible/support#3 so that empty translations and translations without the selected books better manage the mismatching query.

# v2.0.2

- Adds missing Marked JS file

# v2.0.1

- New System Architecture as to how Scripture is added
- New Application Page (Bible Page)
- New Linker (anonymous users) system
- SEO for each chapter of the Bible
- New Easy Sharing System
- New Tagging system
- New Notes system
- New Search system
- Integration with OpenAI