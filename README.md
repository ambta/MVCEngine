![Ambta Logo](https://avatars0.githubusercontent.com/u/10357868?v=3&s=400)

# Ambta MVCEngine
The Ambta MVCEngine is an engine that lets developers build Model, View, Control applications in PHP. The engine is compact and only provides the basic rendering. It is scalable, flexible and easy to use.

# Why use Ambta MVCEngine
Our experience in the field of web development thought us that frameworks are not always the solution.
For example, we develop custom modules for content management systems for customer's on our custom CMS.
We wanted to develop the module's by the rules of MVC but we wanted the CMS and its modules to be separated.
This is where the MVCEngine comes in. We configured the engine to the needs of our CMS and were able to render our modules perfectly.

# Requirements
    PHP 5.4+

# Running a sample

    1. Clone this project somewhere inside an apache www folder, as of now called root.
    2. Navigate from the root folder to: `Samples -> application`. This is an example of a application. It shows the structure the required application structure.
    3. Open up: `ApplicationKernel.php`. Define your custom kernel, but make sure it constructs parent.
    4. Open up: `index.php`. Here you will find the initialization of the engine and the application. Initialization can be called from any apache www file.
    5. Configure `index.php`. Always make sure the engine and application path are set properly.
    6. Open and configure: `Config/Config.json`.
    7. In your browser: Navigate to the application folder and confirm if the application is configured properly.


