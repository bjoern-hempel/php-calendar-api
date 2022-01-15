# API examples Event - `v1`

**Endpoint**: http://localhost/api/v1/events.json

## Get collection (all)

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/events.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
[
  {
    "id": 1,
    "user": "/api/users/1",
    "name": "Angela Merkel",
    "type": 0,
    "date": "1954-07-17T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 2,
    "user": "/api/users/1",
    "name": "Arnold Schwarzenegger",
    "type": 0,
    "date": "1947-07-30T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 3,
    "user": "/api/users/1",
    "name": "Bernhard",
    "type": 0,
    "date": "2100-12-25T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 4,
    "user": "/api/users/1",
    "name": "Björn",
    "type": 0,
    "date": "1980-02-02T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 5,
    "user": "/api/users/1",
    "name": "Carolin Kebekus",
    "type": 0,
    "date": "1980-05-09T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 6,
    "user": "/api/users/1",
    "name": "Daniel Radcliffe",
    "type": 0,
    "date": "1989-07-23T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 7,
    "user": "/api/users/1",
    "name": "Erik",
    "type": 0,
    "date": "1970-09-11T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 8,
    "user": "/api/users/1",
    "name": "Isabel",
    "type": 0,
    "date": "1994-08-18T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 9,
    "user": "/api/users/1",
    "name": "Heike",
    "type": 0,
    "date": "1970-05-06T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 10,
    "user": "/api/users/1",
    "name": "Manuel Neuer",
    "type": 0,
    "date": "1986-03-27T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 11,
    "user": "/api/users/1",
    "name": "Olaf Scholz",
    "type": 0,
    "date": "1958-06-14T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 12,
    "user": "/api/users/1",
    "name": "Otto Waalkes",
    "type": 0,
    "date": "1948-07-22T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 13,
    "user": "/api/users/1",
    "name": "Rico",
    "type": 0,
    "date": "2100-08-18T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 14,
    "user": "/api/users/1",
    "name": "Sebastian",
    "type": 0,
    "date": "1997-05-22T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 15,
    "user": "/api/users/1",
    "name": "Sido",
    "type": 0,
    "date": "1980-11-30T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 16,
    "user": "/api/users/1",
    "name": "Elisabeth II.",
    "type": 0,
    "date": "1926-04-21T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 17,
    "user": "/api/users/1",
    "name": "New York City Marathon",
    "type": 1,
    "date": "2022-11-06T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  },
  {
    "id": 18,
    "user": "/api/users/1",
    "name": "Zrce Spring Break, Croatia",
    "type": 2,
    "date": "2022-06-03T00:00:00+00:00",
    "config": {
      "color": "255,255,255,100"
    }
  }
]
```

</details>

## Get item

```bash
❯ curl -X 'GET' -s http://localhost/api/v1/events/1.json | jq .
```

<details>
  <summary>Click to see example response</summary>

```json
{
  "id": 1,
  "user": "/api/v1/users/1",
  "name": "Angela Merkel",
  "type": 0,
  "date": "1954-07-17T00:00:00+00:00",
  "config": {
    "color": "255,255,255,100"
  }
}
```

</details>
