<?php
/* when we execute this command we figure out the directory name we weanna use, then assert that an application or directory
name of that type doesn't already exist and if we get beyond that we download the nightly version of laravel and save it
to this zip file, and then we extract that zip file to the given directory and finaly we do some cleanup to remove that 
temporary zip file and we load the user, the only other thing that the official laravel installer does  is it notifies you
right away, just because it takes a few seconds to install and the person knows what is happening. */
namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp\ClientInterface;
use ZipArchive;//to open up and extract a zip file we can actualy use native php here

class NewCommand extends Command	
{
	protected $client;
	
	public function __construct(ClientInterface $client) //we are leveraging dependency injection here
	//but when we created the object we forgot to pass that in
	{
		$this->client = $client;
		parent::__construct(); // make sure taht we call the parent classes constructor
	}
    public function configure()
	{
		$this->setName('New')
			 ->setDescription('Create a new Laravel application.')
			 ->addArgument('name', InputArgument::REQUIRED);
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		//assert that the folder doesn't already exist
		$directory = getcwd() . '/' . $input->getArgument('name');
		$output->writeln('<info>Crafting Application...</info>');
		$this->assertApplicationDoesNotExist($directory, $output);
		$this->download($zipFile = $this->makeFileName())//laravel generates a unique makefile name
			 ->extract($zipFile, $directory)
			 ->cleanUp($zipFile); //continue chaining and pass through the zip file 
		//download the nightly version of laravel
		//save it to a temporary zip file that we will then extract
		$output->writeln('<comment>Application ready!</comment>');
		//alert the user		
	}
	
	private function assertApplicationDoesNotExist($directory, OutputInterface $output)
	{
		if (is_dir($directory))
		{
			$output->writeln('Application already exists!');
			exit(1); //something went wrong
		}
		
	}
	
	private function makeFileName()
	{
		return getcwd() . '/laravel_' . md5(time().uniqid()) . 'zip';
	}
	
	private function download($zipFile) //$zipfile = makefilename
	{
		$response = $this->client->get('http://cabinet.laravel.com/latest.zip')->getBody();
		file_put_contents($zipFile, $response);
		return $this; //we want this fluent style interface, so we can continue chaining
		//getbody from the request
	}
	
	public function extract($zipFile, $directory)
	{
		$archive = new ZipArchive;
		$archive->open($zipFile);
		$archive->extractTo($directory);
		$archive->close();
		return $this; //return this just in case we need to continue
	}
	
	private function cleanUp($zipFile)
	{
		@chmod($zipFile, 0777); //supress any warnings, change permissions so we can delite this file
		@unlink($zipFile);
		return $this; //just in case we need any more chaining.
	}
}







/*class SayHelloCommand extends Command	
{
    public function configure()
	{
		$this->setName('sayHelloTo')
			 ->setDescription('Offer a greeting to the given person.')
			 ->addArgument('name', InputArgument::REQUIRED, 'Yourname.')
			 ->addOption('greeting', null, InputOption::VALUE_OPTIONAL, 'Override the default greeting', 'Hello');
	}
	
	public function execute(InputInterface $input, OutputInterface $output)
	{
		$message = sprintf('%s, %s', $input->getOption('greeting'), $input->getArgument('name'));
		$output->writeln("<info>{$message}</info>");	
	}
}

*/