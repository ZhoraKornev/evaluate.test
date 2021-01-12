# evaluate.test 
### API
This project present simple subscription service - we have plans with different tariffs 
all plans contains multiply interest content.

### BUILD
just run make build
Then run make fixtures - and cope auth data from console for start work

### SWAGGER
presents in json 
available at url http://your_domain/api/doc.json

### TESTS
only unit
functional maybe later

### MAIL
- mailhog docker image already build in

#### RESPONSE/REQUEST
- all **RESPONSE** data represent in JSON data.

 - all **REQUEST** data should represent in JSON
   - only login method request should be in form-data 
   - headers must contain security with generated JWT token
     - all secured link should receive headers with "Authorization" key and "JWT" as value
     - JWT generated after login. Lifetime JWT 50 minutes and then you relogin no refresh token allowed

### SOME LOGIC
- HOME(unsecure) page represent all subscriptions plan and short welcome message to users
- AUTH(unsecure) *auth/login* page await for form-data with POST method = email and password
- PAY callback url(unsecure) *api/v1/subscription/pay* - waiting  for request form payment gateway
with JSON data for example we use FONDY payment system https://docs.fondy.eu/en/docs/page/5/#chapter-5-4-json
- fixtures always return random data, so you need to be careful read console message 
- MAKE command exists for fixtures     
  


  - ORDERing new subscription logic = we create new non_active user_subscription with a nullable field activated_at
when we receive correct data from payment system we will deactivate all current subscription and set only ONE active subscription 
    with correct due_date 
  - order number or ORDER_ID resolve in this format user_id|subscription_id
  - user_subscription will be deactivating by rabbit service UnsubscribeConsumer with delaying_time = subscription_period_time
    

some non critical TODO
- move all docker volume in one place(with json LOG)
- subscription_user status rebuild onto state machine pattern
- extend payments system with polymorphic methods 
- create a controller for admin
- log all events to mailhog
