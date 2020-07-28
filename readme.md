# About

This is a solution to the coding challenge given to me by holla tags.

## The problem

We need to make a request to a billing API to bill about 10,000 users within 1 hr of which the api takes 1.6 secs to process and respond to each request. I am also required to suggest an approach to scale the above if we need to bill 100,000 users within 4.5hrs.

## The solution

Billing each user per request is not ideal as it would take approximately 4.4hrs (1.6x10,000 secs) to bill all. To optimize performance will can bill a set number of users per request by performing asynchronous requests to the api endpoint. This means we can make for example, 10 api calls at the same time to our billing api. So we can run a cron scheduler to make a request to bill a set number of users every minute (or 5 minutes depending on the server's cron policy). It is also good we set the request timeout to 2secs for unforseen circumstances in the api server that would make it process a request for longer than 1.6 secs.

### Making Asycn Requests

There are two ways we can make an asynchronous request. We can use the in-built curl_multi_exec php function or Guzzle Http library. We'll use the latter for easier implementation. See [Making concurrent requests](https://guzzle.readthedocs.io/en/latest/quickstart.html#concurrent-requests) for more information.

### Billing 10,000 users

Because of network latency, we will assume that it takes approximately 2 seconds to complete a request. We'll also assume we've setup our cron to run our billing script every 2 seconds.

So, we need to find out how many users to bill every 2 seconds if we are to bill 10,000 users within 3,600 seconds (1hr). We can bill approximately **6 users** ((2secs x 10,000 users) / 3,600 secs) every 2 seconds.

### Billing 100,000 users

Using the same assumption for billing 10,000 users, we need to find out how many users to bill every 2 seconds if we are to bill 100,000 users in 16,200 secs (4.5hrs). We can bill approximately **13 users** ((2secs x 100,000 users) / 16,200 secs) every 2 seconds.

### Code Implementation

I have written a sample code in [biller.php](biller.php) to implement the above solution.
