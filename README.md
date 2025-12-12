# 🔐 FantumID

FantumID is the identity and authentication service of the **Fantum Cloud Suite**.  
It manages user accounts, generates colored default avatars, and provides a complete OAuth 2.0 implementation for both **client/secret** and **service/secret** authentication flows used by official Fantum services.

It also handles:
- ✉️ Email validation
- 🔑 Email-based 2FA codes
- 🔐 Secure session authentication
- 🎨 Avatar generation

Made with Symfony ❤️


## ✨ Features

- **User registration & login system** (Symfony Security)
- **OAuth 2.0** code and token generation
- **Service-to-service authentication**
- **Default avatar generator** producing colored SVG/PNG avatars
- **Email validation workflow**
- **2FA via one-time email codes**
- **User profile endpoint**
- **Admin or service user listing**


## 📡 Routes

#### Avatar
- `GET /avatar/{id}`  
Returns the generated default avatar for a given user ID.

#### Authentication
- `GET /register`  
- `POST /register`  
Standard Symfony registration form and submission.

- `GET /login`  
- `POST /login`  
Symfony login form and authenticator processing.  
(`GET` shows the login form, `POST` is handled by the security authenticator.)

- `POST /logout`  
Symfony-enabled logout route (method defined in `security.yaml`).  
Typically called by a `POST` request since Symfony blocks `GET` logout for security reasons.

#### OAuth 2.0
- `POST /oauth/access_token`  
Generates an access token from a valid code or service credentials.

- `GET /oauth/code`  
Generates an OAuth code for users authenticating via client/secret.

- `GET /oauth/code/service`  
Generates a service OAuth code using service/secret credentials.

#### API
- `GET /api/me`  
- `PATCH /api/me`  
Retrieve or update the authenticated user's profile.

- `GET /api/users`  
List users (permissions depend on the service or user role).


## OAuth 2.0 Flow 🔁

### Standard client/secret flow
```
Client → FantumID (/oauth/code) → Client exchanges code at /oauth/access_token
```

### Service/Secret flow (for internal fantum cloud services)
```
Service → FantumID (/oauth/code/service) → Service exchanges code at /oauth/access_token
```


## Avatar Generation 🎨

FantumID automatically generates a colored default avatar.

The avatar can be retrieved via `GET /avatar/{id}`
