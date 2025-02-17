# URL Shortening System

## Introduction

The URL shortening system is a web application designed and implemented in PHP for shortening Uniform Resource Locators (URLs). A user provides a long URL, and the system returns a short URL. Upon requesting the short URL, the original long URL is returned.

---

## Functional Requirements

- **Shortening URLs**: Given a long URL, the system returns a unique short URL.
- **Retrieving Long URL**: Given a short URL, the system returns the original long URL.
- **Expiration Time**: Each URL has an expiration time, and expired URLs should be deleted.
  - Default expiration time is **5 minutes**, configurable.
- **Prevent Duplicate Short URLs**: If a user attempts to shorten a URL that already exists (and isn't expired), the system returns the already shortened URL but resets its expiration time.
- **Click Tracking**: The system tracks the number of clicks for each shortened URL.
- **Authentication**: All requests need to be authenticated using either **JWT** or **OAuth2**.
- **Expiration Overwrite**: Authenticated users can overwrite the expiration time of an already shortened URL and can access any shortened URL.

---

## Non-Functional Requirements

- **Framework**: The system is implemented using either **Laravel 9+** or **Symfony 6+**.
- **RESTful APIs**: The APIs follow RESTful conventions for accessing the resource server.
- **OpenAPI Documentation**: Provide OpenAPI documentation for the APIs.
- **Dependency Management**: Use a dependency management framework of your choice.
- **Database**: The system uses either a relational or non-relational database.
- **Test Coverage**: The system includes at least **80% test coverage**.
- **BONUS**: Optionally, a frontend application can be built using **Vue.js** or **React.js**.
- **Optional**: Dockerize the entire application using **Docker** and **Docker Compose**.


To start the scheduler, use the following command:

```bash
docker compose up -d --build
```
