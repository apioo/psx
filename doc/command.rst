
Command
=======

This chapter gives a short overview of the most important methods which you need
inside an command. To simplify things take a look at the following source code

.. code-block:: php

    <?php

    use PSX\CommandAbstract;
    use PSX\Command\OutputInterface;
    use PSX\Command\Parameter;
    use PSX\Command\Parameters;

    class Command extends CommandAbstract
    {
        public function doExecute(Parameters $parameters, OutputInterface $output)
        {
        	// returns whether an parameter is available
        	$parameters->has('bar')

        	// returns the value of an parameter
        	$bar = $parameters->get('bar')


        	// inside the command you can access every service from the DI
        	// container
        	$this->getEntityManager();

            // the command can write informations about the process. By default
            // this output gets written to stdout if we are in CLI mode. You
            // can also use i.e. the logger OutputInterface which writes the
            // messages to an psr compatible logger
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

A command is a piece of code which should be idempotent so you can run it 
multiple times without breaking things. By slitting up your code in commands you 
can execute specific states of your application later in case something goes 
wrong. I.e. if you have a command which sends an email if an payment process was 
successful you could execute this command directly from the controller and also 
from an cron in order to complete missing payments.

The command can be executed from an controller or another command. If an 
required parameter is missing an exception gets thrown.

.. code-block:: php

    <?php
    
    $this->getExecutor()->run(new Map('Foo\Bar\Command', array('bar' => 'foo')));

It is also possible to invoke the command through the console

.. code-block:: none

    vendor\bin\psx Foo\Bar\Command -bar foo

