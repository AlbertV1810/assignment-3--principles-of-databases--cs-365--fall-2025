# Fall 2025 Principles of Databases — Assignment 3

* **Do not start this project until you’ve read and understood these instructions. If something is not clear, ask.**

---

## ❖・Introduction・❖

In assignment 2, you created a database consisting of relations — or flat tables — of passwords associated with web sites/apps. For this assignment, you’ll extend on assignment 2 by doing the following:

* Separating the content in any flat tables into separate tables;
* Establishing relations between those tables;
* Adding key constraints;
* Graphically drawing out the relationships between those tables using the ER model; and,
* Adding an interactive layer using HTML forms and PHP.

In essence, you’re creating an elaborate, MVC-based application of assignment 2 using a WAMP/MAMP stack.

---

## ❖・Requirements・❖

The database should handle the following operations, all via HTML forms:

* **Search** every component of the database, wrapping the result, including any failed search queries, in an HTML table that’s presented to the user.
* **Update** any component using another component(s) as a pattern match. For example, update an existing URL based on the site/app name.
* **Insert** new entries/tuples into the database. Each new entry should accept site/app name, URL, email address, username, password, and a comment. The comment field in the HTML form should use the HTML [`textarea`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/textarea) element in lieu of the `input` element.
* **Delete** an entry from the database based on a pattern match with one or more existing components.

Additionally…

* Include a button — prominently placed — that adds the ability to clear the results.
* Draw a diagram of your entity-relationship, or ER, model. You may do this on paper and then take a picture or scan it, or you may use a graphical digital tool of your choosing.

---

## ❖・Rules・❖

* Paths used in any of the HTML or PHP files must be relative; **no** absolute paths.
* The database must be called `student_passwords`.
* The database username must be `passwords_user`. **Do not establish a password for this user.**
* Standing up the database must be carried out by loading `setup.sql` from within the `mysql` folder. (See the included `mysql` folder.) For example, running `mysql -u root -p < setup.sql` should do everything required to stand up the database. Alternatively, logging in to MySQL, via `mysql -u root -p` from within the `mysql` folder, then, once logged in, running `source setup.sql`, should do the same.
* Your ER model image must be placed in the included `entity-relationship-model` folder.
* Ensure the name of your ER model image is one of either `er-model.jpg`, `er-model.png`, or, `er-model.pdf`. No other file name or type is allowed. Also note that **the file may not be larger than 1MB**. Use an image editor, such as [GIMP](https://www.gimp.org/), if you need to further reduce the size of your image.
* `php/config.php` should contain the database credentials required for the user to log in. Recall that these credentials should also be included in `mysql/setup.sql`.
* `php/helpers.php` should contain functions requiring interactions with the database. In previous examples, this file has been called `db.php`.
* Do not edit any of the `.gitignore` files.
* Do not add more files to this repo; all files required for this project are already included. If you must add one or more files, because you found an error in this assignment’s scaffold, first ask in our class’ Microsoft Group so everyone is aware of the mistake.
* You must use EditorConfig, per the included `.editorconfig` file.

---

## ❖・Tips・❖

* Clone this repo into your `htdocs` folder if you’re a Windows user, or the `Sites` folder if you’re a Mac user. Don’t rely entirely on PhpStorm’s `localhost` for the development of this project.
* Note that Windows is usually configured by default to hide extensions, so you’ll need to be careful about a file that has the same extension twice, which would be ignored by this repo. For example, you might think you named your ER model file `er-model.png`, when, in fact, it might actually have been called `er-model.png.png`.
* You may reuse any and all of the CSS from our examples repo, or any other CSS you like.

---

## ❖・Due・❖

Monday, 17 November 2025, at 2:10 PM.

---

## ❖・Grading・❖

| Item                     | Points |
|--------------------------|:------:|
| *Project implementation* | `33`   |
| *Syntax quality*         | `33`   |
| *Following instructions* | `33`   |

---

## ❖・Submission・❖

**NO late submissions will be accepted.**

You will need to issue a pull request back into the original repo, the one from which your fork was created for this project. See the **Issuing Pull Requests** section of [this site](http://code-warrior.github.io/tutorials/git/github/index.html) for help on how to submit your assignment.

**Note**: This assignment may **only** be submitted via GitHub. **No other form of submission will be accepted**.
