{
  "swagger": "2.0",
  "info": {
    "description": "Try Operation <?php echo $_SERVER['REQUEST_SCHEME']; ?>://<?php echo $_SERVER['SERVER_NAME']; ?><?php echo $basePath = strstr($_SERVER['REQUEST_URI'],'/public') ? explode("/public", dirname($_SERVER['REQUEST_URI']))[0].'/public/api/v1' : '/api/v1'; ?> + path to test it. You can see the response right here",
    "version": "1.0.0",
    "title": "Bracket API",
    "termsOfService": "",
    "contact": {
      "email": "atul.gupta@appster.in"
    },
    "license": {
      "name": "Apache 2.0",
      "url": "http://www.apache.org/licenses/LICENSE-2.0.html"
    }
  },
  "host": "<?php echo $_SERVER['SERVER_NAME']; ?>",
  "basePath": "<?php echo $basePath; ?>",
  "tags": [
    {
      "name": "user",
      "description": "Everything about Users",
      "externalDocs": {
        "description": "Find out more",
        "url": ""
      }
    },
    {
      "name": "umoji",
      "description": "Everything about Umojis"
    }
  ],
  "schemes": [
    "<?php echo $_SERVER['REQUEST_SCHEME']; ?>"
  ],
  "paths": {
    "/user/sign-in": {
      "post": {
        "tags": [
          "user"
        ],
        "summary": "login a user to the app",
        "description": "",
        "operationId": "loginUser",
        "consumes": [
          "application/json"
        ],
        "produces": [
          "application/json"
        ],
        "parameters": [
           {
            "in": "body",
            "name": "body",
            "description": "Sign in object that needs to be login the APP",
            "required": true,
            "schema": {
              "$ref": "#/definitions/SignIn"
            }
          }
        ],
        "responses": {
          "200": {
            "description": "User Successfully Login",
            "schema": {
              "$ref": "#/definitions/User"
            }
          },
          "401": {
            "description": "Server Error"
          }
        }
      },
    },
    "/user/sign-up": {
      "post": {
        "tags": [
          "user"
        ],
        "summary": "Add a new user to the app",
        "description": "",
        "operationId": "addUser",
        "consumes": [
          "application/json"
        ],
        "produces": [
          "application/json"
        ],
        "parameters": [
           {
            "in": "body",
            "name": "body",
            "description": "Sign Up object that needs to be added to the APP",
            "required": true,
            "schema": {
              "$ref": "#/definitions/SignUp"
            }
          }
        ],
        "responses": {
          "200": {
            "description": "User Successfully Created",
            "schema": {
              "$ref": "#/definitions/User"
            }
          },
          "401": {
            "description": "Server Error"
          }
        }
      },
    },
  },
  "securityDefinitions": {
    "petstore_auth": {
      "type": "oauth2",
      "authorizationUrl": "http://petstore.swagger.io/oauth/dialog",
      "flow": "implicit",
      "scopes": {
        "write:pets": "modify pets in your account",
        "read:pets": "read your pets"
      }
    },
    "api_key": {
      "type": "apiKey",
      "name": "api_key",
      "in": "header"
    }
  },
  "definitions": {
    "User": {
      "type": "object",
      "properties": {
        "email": {
          "type": "string"
        },
        "status": {
          "type": "integer"
        },
        "userId": {
          "type": "integer"
        },
        "userName": {
          "type": "string"
        },
        "profileImage": {
          "type": "string"
        },
        "isNotification": {
          "type": "integer"
        },
        "isResetPassword": {
          "type": "integer"
        },
        "isPublic": {
          "type": "integer",
          "description": "1 (Public Profile) / 0 (Private Profile)"
        },
        "isProfileComplete": {
          "type": "integer",
          "description": "1 (Profile Completed) / 0 (Profile Not Completed)"
        },
        "followerCount": {
          "type": "integer",
          "description": "Total follower count"
        },
        "followingCount": {
          "type": "integer",
          "description": "Total Following count"
        },
        "profileImagePath": {
          "type": "string",
          "description": "Full Image Path"
        }
      }
    },
    "Tag": {
      "type": "object",
      "properties": {
        "id": {
          "type": "integer",
          "format": "int64"
        },
        "name": {
          "type": "string"
        }
      }
    },
    "deviceInfo": {
      "type": "object",
      "properties": {
        "deviceType": {
          "type": "integer",
          "format": "int64",
          "example": "1"
        },
        "deviceToken": {
          "type": "string"
        }
      }
    },
    "SignIn": {
      "type": "object",
      "required": [
        "email",
        "password",
        "deviceInfo"
      ],
      "properties": {
        "email": {
          "type": "string",
          "example": "test@test.com"
        },
        "password": {
          "type": "string",
          "example": "123456"
        },
        "deviceInfo": {
          "$ref": "#/definitions/deviceInfo"
        }
      }
    },
    "SignUp": {
      "type": "object",
      "required": [
        "email",
        "password",
        "deviceInfo"
      ],
      "properties": {
        "email": {
          "type": "string",
          "example": "test@test.com"
        },
        "password": {
          "type": "string",
          "example": "123456"
        },
        "deviceInfo": {
          "$ref": "#/definitions/deviceInfo"
        }
      }
    },
    "ApiResponse": {
      "type": "object",
      "properties": {
        "status": {
          "type": "integer"
        },
        "statusCode": {
          "type": "integer"
        },
        "message": {
          "type": "string"
        },
        "result": {
          "type": "object"
        }
      }
    }
  },
  "externalDocs": {
    "description": "Find out more about Swagger",
    "url": "http://petstore.swagger.io/#/user"
  }
}