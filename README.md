# home-portal
Home portal for my local server

# TODO:
 - [ ]: Add further login protections through nginx
 - [ ]: Add nginx auth-check to the whole site and not just pihole
 - [ ]: Add basic buttons to redirect to services
 - [ ]: Add pihole service button

 ## Pasword Hash for the json user file
 ```php -r 'echo password_hash("tu_password_aqui", PASSWORD_DEFAULT) . "\n";'```