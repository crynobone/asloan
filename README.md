# ASLoan 

[![test](https://github.com/crynobone/asloan/workflows/test/badge.svg)](https://github.com/crynobone/asloan/actions?query=workflow%3Atest)

## Installation

Install the repository from GitHub.

```
git clone git@github.com:crynobone/asloan.git
cd asloan
```

Run Composer installation.

```
composer install --prefer-dist
composer run post-root-package-install
composer run post-create-project-cmd
```

Run the default migrations

```
php artisan migrate
```

Run the application using Laravel Serve

```
php artisan serve
```

Which going to output similar to following, you can access to page using the generated URL:

```
> PHP 7.4.3 Development Server (http://127.0.0.1:8000) started
```

## User Stories

### Loan Applications

* User can apply a loan.
* User can apply a loan with a specific term expiry date.
* User can apply for more than 1 loan.
* User can't apply loan with zero or negative amount.
* User can't apply loan with invalid loan duration (start date after end date).
* Application should be able to calculate next loan due date.
* Application should be able to calculate next loan due amount.
* Application should ensure due amount is equals to total loan balances if loan term ends less than 1 week.
* Application should ensure that customer shouldn't make first repayment in less than 4 days.

### Loan Repayments

* User can make repayment to a loan.
* User can make full settlement to a loan. (handle by code but not UI)
* User can make repayment to a loan on specific time.
* User can't make repayment to a loan on different currency.
* User can't make repayment to a loan higher than outstanding amount.
* Application should disallow repayment if loan has no balance (loan completed).

#### Scenario: Loan has outstanding balance

* Application should create repayment schedule and repayment amount.
* Application should update `due_amount` and `due_at` after each repayment.
* `due_amount` and `due_at` should reset after repayment occured.

### Scenario: Loan has no outstanding balance (full settlement)

* Application shouldn't create repayment schedule.
* Application shouldn't update `due_amount` and `due_at`.
* Application need to ensure Loan has `completed_at`.

<iframe width="100%" height="500px" style="box-shadow: 0 2px 8px 0 rgba(63,69,81,0.16); border-radius:15px;" allowtransparency="true" allowfullscreen="true" scrolling="no" title="Embedded DrawSQL IFrame" frameborder="0" src="https://drawsql.app/orchestra-platform/diagrams/loan-application/embed"></iframe>
