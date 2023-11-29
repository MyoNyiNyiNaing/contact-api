
# Contact Api


## API Reference

### Authentication

#### Login (Post)

```http
  https://localhost:8000/api/v1/login
```

| Arguments | Type     | Description                |
|--------- |-------- |-------------------------- |
| `email` | `string` | **Required** admin@gmail.com |
| `password` | `string` | **Required** admin123 |


#### Register (Post)

```http
  https://localhost:8000/api/v1/register
```

| Arguments | Type     | Description                |
|--------- |-------- |-------------------------- |
| `name` | `string` | **Required** example |
| `email` | `string` | **Required** example@gmail.com |
| `password` | `string` | **Required** asdffdsa |
| `password_confirmation` | `string` | **Required** asdffdsa |


#### Logout (POST)

```http
  https://localhost:8000/api/v1/user-logout
```

#### Logout All (POST)

```http
  https://localhost:8000/api/v1/user-logout-all
```


### Contact

#### Get Contacts (Get)

```http
  https://localhost:8000/api/v1/contact
```


#### Search Contacts (Get)

```http
  https://localhost:8000/api/v1/contact?keyword=elonmusk
```


#### Get Single Contact (Get)

```http
  https://localhost:8000/api/v1/contact/{id}
```

#### Create Contact(POST)

```http
  https://localhost:8000/api/v1/contact
```

| Arguments | Type     | Description                |
|--------- |-------- |-------------------------- |
| `name` | `string` | **Required** Elon Musk |
| `phone` | `integer` | **Required** 095146124 |
| `email` | `string` | **Nullable** elon@example.com |
| `address` | `string` | **Nullable** NewYork |
| `photo` | `png,jpeg` | **Nullable** Profile pic |

#### Update Contact(PUT)

```http
  https://localhost:8000/api/v1/contact/{id}
```
  #### You can update with only singe Parameter or more (Add _method=put to update file with post method)

| Arguments | Type     | Description                |
|--------- |-------- |-------------------------- |
| `name` | `string` | **Required** Elon Musk |
| `phone` | `integer` | **Required** 095146124 |
| `email` | `string` | **Nullable** elon@example.com |
| `address` | `string` | **Nullable** NewYork |
| `photo` | `png,jpeg` | **Nullable** Profile pic |

#### Delete Contact (DELETE)

```http
  https://localhost:8000/api/v1/contact/{id}
```

### Trash 

#### Get Trash (GET)

```http
  https://localhost:8000/api/v1/trash/contact
```

#### Get Single Trash (GET)

```http
  https://localhost:8000/api/v1/trash/contact/{id}
```

#### Force Delete Contact (DELETE)

```http
  https://localhost:8000/api/v1/trash/contact/{id}?delete=force
```

#### Restore Deleted Contact (DELETE)

```http
  https://localhost:8000/api/v1/trash/contact/{id}?delete=restore
```

### Profile

#### Get User Profile (GET)

```http
  https://localhost:8000/api/v1/user-profile
```

#### Change Password (POST)

    # https://localhost:8000/api/v1/change-password

| Arguments | Type     | Description                |
|---------- |----------|----------------------------|
| `current_password` | `string` | **Required** admin123 |
| `password` | `string` | **Required** asdffdsa |
| `password_confirmation` | `string` | **Required** asdffdsa |







