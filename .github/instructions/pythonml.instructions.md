---
applyTo: '**'
---
The purposer of this file is to provide instructions for the python machine
learning micro-service to be implemented and connected to the main SCM laravel
application.

Context: Understand the following information and use it to guide your
decisions. The purpose of the ml microservice is to;
1. Carry out customer segementation based on demographics of retailers and their sales trends
2. Create predictions for Aktina(sales where a seller has company name as Aktina) sales based on the previous sales trends.
You will have to use the retailer table demogrpahics data like average age, income and other fields
and their corresponding sales based on the company name to segement customers.
use the sales data for Aktina company to predict the sales of Aktina

instructions
- Use the ml-ideas.txt to guide your approach and help you make the right choices
- ensure to first understand the migrattions and modes involved ie retailer, orders, etc
- Create a detailed plan to implement the api, services, repositories, livewire components to be add
- Add the necessary graphs tables and components to the admin predicions blade view only, it should
containe information about the sales predictions and the customer segmentation.
- Only add waht is abouslutely necessry, avoid bloat and unecessay code
- use good design patterns and KISS.
- ensure to first add basic fucntionality all the way to the views before adding complex implemenations.
