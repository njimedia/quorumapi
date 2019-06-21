# Quorum Public Affairs API Client

An API for interacting with Quorum public affairs services.

API Explorer: https://www.quorum.us/api

## Example
```
$quorum = new NJIMedia\QuorumAPI\Client('username', 'apikey' );
$is_valid = $quorum->validate();
```

## Todo
Much of the client API is not complete. Currently this client
handles only a few basic subscriber interactions.

## Contributing
Pull requests welcome.
phpcs.standard: PSR2
