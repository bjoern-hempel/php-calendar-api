# API examples User - v1

**Endpoint**: http://localhost/api/v1/users.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "email": "user1@domain.tld",
    "username": "user1",
    "firstname": "Firstname 1",
    "lastname": "Lastname 1",
    "roles": [
      "ROLE_USER"
    ]
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "email": "user1@domain.tld",
  "username": "user1",
  "firstname": "Firstname 1",
  "lastname": "Lastname 1",
  "roles": [
    "ROLE_USER"
  ]
}
```

</details>

## Get extended item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/users/1/extended.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "email": "user1@domain.tld",
  "username": "user1",
  "firstname": "Firstname 1",
  "lastname": "Lastname 1",
  "roles": [
    "ROLE_USER"
  ],
  "events": [
    "/api/events/1",
    "/api/events/2",
    "/api/events/3",
    "/api/events/4",
    "/api/events/5",
    "/api/events/6",
    "/api/events/7",
    "/api/events/8",
    "/api/events/9",
    "/api/events/10",
    "/api/events/11",
    "/api/events/12",
    "/api/events/13",
    "/api/events/14",
    "/api/events/15",
    "/api/events/16",
    "/api/events/17",
    "/api/events/18"
  ],
  "images": [
    "/api/images/1",
    "/api/images/2",
    "/api/images/3",
    "/api/images/4",
    "/api/images/5",
    "/api/images/6",
    "/api/images/7",
    "/api/images/8",
    "/api/images/9",
    "/api/images/10",
    "/api/images/11",
    "/api/images/12",
    "/api/images/13"
  ],
  "calendars": [
    "/api/calendars/1"
  ]
}
```

</details>
