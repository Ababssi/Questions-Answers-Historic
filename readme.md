# How to run the project

## This is the command to build and up the project : *make launch*

## This is the command to perform a composer install : *make install*

## This is the command to execute tests : *make test*

## Available API routes with Payload examples:

### QuestionController

1. POST /questions
```
{
  "title": "Qu'est ce qui vous appartient bien que les gens l'utilisent plus que vous ?"
  "status": "draft",
  "promoted": false
}
```
2. PUT /questions/{id}
```
{
  "title": "Un berger possède 27 brebis. Toutes meurent sauf 8. Combien en reste-t-il ?",
  "status": "published",
  "promoted": false
}
```
3. GET /questions/{id}
4. GET /questions
5. DELETE /questions/{id}


### AnswerController

1. POST /answers
```
{
   "channel": "bot",
   "body": "8... Si vous avez répondu 19 c'est que vous vous êtes fait avoir comme moi la première fois que j'ai entendu cette devinette !"
}
   ```
2. PUT /question/{id}/answer
```
{
   "channel": "faq",
   "body": "Votre prénom."
}
   ```
3. GET /answers/{id}
4. DELETE /answers/{id}


## Spec : 
1. php 8.2 symfony 6.3
2. Database : postgresql
3. Docker
4. Tests : phpunit

## Structure Database
![Capture d’écran 2023-07-12 à 17 35 07](https://github.com/smart-tribune/backend-test-hiring-Ababssi/assets/84496529/d25ffc8b-964c-486b-827f-02e75b61b310)


