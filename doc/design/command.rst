
Command
=======

Abstract
--------

A command is a piece of code which takes some arguments and executes a specific
task. You can see a command like a controller but without the request/response 
context. A command can be executed from the command line or from a 
controller/command. I.e. if you have a command which sends an email if a 
payment process was successful you could execute this command directly from the 
controller and also from a cron in order to complete payments. In contrast to 
a symfony command a psx command was not designed for cli that means you can 
not access user input. All settings which the command needs must be available in 
the parameters. But we have build a symfony command which takes the user input
and triggers a PSX command.

Usage
-----

This chapter gives a short overview of the most important methods which you need
inside an command. To simplify things take a look at the following source code.

.. code-block:: php

    <?php

    namespace Foo\Bar;

    use PSX\CommandAbstract;
    use PSX\Command\OutputInterface;
    use PSX\Command\Parameter;
    use PSX\Command\Parameters;

    class Command extends CommandAbstract
    {
        /**
         * @Inject
         * @var PSX\Http
         */
        protected $http;

        public function onExecute(Parameters $parameters, OutputInterface $output)
        {
            // returns whether an parameter is available
            $parameters->has('bar')

            // returns the value of an parameter or null
            $bar = $parameters->get('bar')

            // inside the command you can use every injected service
            $this->http->request(...);

            // the command can write informations about the process. By default
            // this output gets written to stdout if we are in CLI mode else to
            // the logger
            $output->writeln('Some processing informations');
        }

        /**
         * Returns all parameters which are required for this command
         *
         * @return PSX\Command\Parameters
         */
        public function getParameters()
        {
            return $this->getParameterBuilder()
                ->addOption('bar', Parameter::TYPE_REQUIRED, 'The magic foo parameter')
                ->getParameters();
        }
    }

The command can be executed from a controller or another command. If a
required parameter is missing an exception gets thrown.

.. code-block:: php

    <?php
    
    $this->executor->run(new Map('Foo\Bar\Command', array('bar' => 'foo')));

It is also possible to invoke the command through the console. Therefor you have
to pass the parameters as arguments

.. code::

    $ ./vendor/bin/psx command Foo\Bar\Command bar:foo

You could also pipe the parameters as JSON string through stdin with the "-s" 
switch

.. code::

    $ echo {"bar": "foo"} | ./vendor/bin/psx command -s Foo\Bar\Command

Generation
----------

It is possible to generate a command template. You can use the following 
command which takes as first argument the class name and as second a comma 
seperated list with service names. These services are automatically included in
the command

.. code::

    $ ./vendor/bin/psx generate:command Acme\Command connection,http

