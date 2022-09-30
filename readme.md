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

Constraints : 
Answers.channel value is restricted to "faq" or "bot"
Status value is restricted to "draft" or "published"


##### Step 2:

1. Update existing Q&A to change the value of the title and the status
2. Listen to changes on the question and populate **asynchronously** a new entity HistoricQuestion with those changes

##### Step 3:

1. Create an exporter service which is be able to export any entity type content into CSV file. (Don't forget Unit tests)
2. Use the previously created exporter in order to export HistoricQuestion datas

##### Step4:

1. Dockerize the project and provide readme file to run the project
