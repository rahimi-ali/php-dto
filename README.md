# PHP-DTO
## DTOs for PHP!

DTOs can be used to transform Incoming Psr7 server requests, json and random arrays to typed classes with validation built in.

Does not use reflection and tries to handle everything with good old classes and methods!

### Notes
- Do not set readonly or private attributes for your DTO classes as the parent constructor which handles the hydration of DTOs cannot access them.


### License
MIT
