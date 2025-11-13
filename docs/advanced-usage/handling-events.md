---
title: Handling events
weight: 5
---

The packages will fire these events for you to hook into:

- `OneTimePasswordSuccessfullyConsumed`: fired when a one-time password is successfully used.
- `FailedToConsumeOneTimePassword`: fired when a one-time password fails to be used.

You can use these events to perform actions when a one-time password is successfully used or fails to be used. For example, you can use these events to log the usage of one-time passwords using [our activity log package](https://spatie.be/docs/laravel-activitylog).

