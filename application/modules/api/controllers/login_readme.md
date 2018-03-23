Author @Shivam Shukla

Required External Libraries/Files:


* Application/Helpers/Common_helper.

* Application/Config/Contants

* Application/Language/English/Rest_Controller_Lang.

Allowed Method: 

1. Post (form-data/multipart)

Api-Flow:

* Firstly we validate the credentials from user after that we create a session array
and insert it in session table and it can used in same way like we use session after 
signup

* We check if our app support multi-device login from constant file and it is then
we update the session info each time user login in app and otherwise if user login with
same device then we update it otherwise we create a new row in session table.





