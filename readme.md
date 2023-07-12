## Smart Tribune - Backend - Coding Test :

Ideally use PHP8 & Symfony6 (but otherwise Kotlin) and a relational database (mariadb/postgre).  
**Unit Tests** are mandatory.  
Provide a documentation / readme file to explain how to run the project.  
Source code should be versioned within a git repository.

#### Introduction 

Based on the following JSON payload as Question & Answer document

```
{
	"title": varchar 100 - required
	"promoted": boolean - required,
	"status": enum - required 
	"answers": [{
		"channel": enum - required,
		"body": varchar 500 - required
	}]
}
```

##### Step 1:

Create an API to validate a Q&A and store into a database with following extra fields : createdAt, updatedAt  
Do not use ApiPlatform for this test.

Constraints : 
Answers.channel value is restricted to "faq" or "bot"
Status value is restricted to "draft" or "published"


##### Step 2:

1. Update existing Q&A to change the value of the title and the status
2. Listen to changes on the question and populate ***asynchronously*** a new entity HistoricQuestion with those changes

##### Step 3:

1. Create an exporter service which is be able to export any entity type content into CSV file. (Don't forget Unit tests)
2. Use the previously created exporter in order to export HistoricQuestion datas

##### Step4:

1. Dockerize the project and provide readme file to run the project


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

#### Bonus generate csv file
1. GET /historicQuestion/export
2. GET /questions/export


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

#### Bonus generate csv file
1. GET /answers/export

## Spec : 
1. php 8.2 symfony 6.3
2. Database : postgresql
3. Docker
4. Tests : phpunit
