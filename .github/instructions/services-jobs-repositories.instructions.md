---
applyTo: '**'
---
This instructions file is to guide you in implementing the services, jobs, and repositories for the application. The goal is to create a robust and scalable architecture that follows best practices and design patterns.
- Use the Service-Repository pattern to organize your code.
- When creating a services or repositories or jobs from a request, use the instructions in the plan instructions file to 
create a plan outlining the steps needed to implement the service, repository, or job.
- Ensure that the services and repositories are well-documented and follow the existing naming conventions.
- Use the existing services and repositories in the `app/Services` and `app/Repositories
- All service must be modular and reusable, avoid creating monolithic services.
- Use dependency injection to manage dependencies and avoid tight coupling.
- Ensure that the services and repositories are testable and follow best practices for unit testing.
- Use Laravel's built-in features for caching, queuing, and scheduling where appropriate.
- Only cache data that is expensive to compute or retrieve, and ensure that the cache is invalidated when the underlying data changes.
- Use jobs for long-running tasks or tasks that can be processed asynchronously.
- Ensure that jobs are idempotent and can be retried without side effects.
- Use repositories to encapsulate data access logic and provide a clean interface for the services.
- Avoid being to verbose in your responses, be concise and to the point.
- Provide advice for best practices and design patterns where necessary and suggest better implementations
during the planning phase.
- Ensure the services, repositories, and jobs are consistent with the overall architecture of the application including 
controllers, models, views and routes.
- Use existing services, repositories, and jobs as a reference for creating new ones.