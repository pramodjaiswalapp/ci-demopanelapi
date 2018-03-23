Author @Shivam Shukla

Required External Libraries/Files:

* Application/Commonfn.

* Application/Helpers/Common_helper.

* Application/Controller/Request {For sending Welcome Asynchronously}.

* Application/Views/Mail/welcome {For sending Welcome Asynchronously}.

* Application/Config/Contants

* Application/Language/English/Rest_Controller_Lang.


Allowed Method: 

1. Post (form-data/multipart)

Api-Flow:

* We create the list of required fields and add there data in the users table and
also simultaneously create a session info and insert it session table, later on 
this session info is required to validate the user existing session validate if
he is liable to hit the api later on after signup

* We send a welcome mail to user acknowledging that they have registered with us.





