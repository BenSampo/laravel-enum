<?php

namespace BenSampo\Enum\Commands;

use hanneskod\classtools\Iterator\ClassIterator;
use ReflectionClass;
use BenSampo\Enum\Enum;
use Illuminate\Console\Command;
use \Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

class EnumAnnotateCommand extends Command
{
    const DEFAULT_SCAN_FOLDER = 'Enums';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'enum:annotate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate annotations for enum classes';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    protected function getArguments(): array
    {
        return [
            ['class', InputArgument::OPTIONAL, 'The class name to generate annotations for'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['folder', null, InputOption::VALUE_OPTIONAL, 'The folder to scan for enums to annotate'],
        ];
    }

    /**
     * Handle the command call.
     *
     * @return void
     * @throws \ReflectionException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if ($this->argument('class')) {
            $this->annotateClass($this->argument('class'));
            return;
        }

        $this->annotateFolder($this->getScanPath());
    }

    /**
     * Annotate any `Enum` classes in a given folder
     *
     * @param string $path
     *
     * @return void
     * @throws \ReflectionException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function annotateFolder(string $path)
    {
        $finder = new Finder();
        $classes = new ClassIterator(
            $finder->files()->in($path)->name('*.php')
        );

        $classes->enableAutoloading();

        /** @var ReflectionClass $reflection */
        foreach ($classes as $reflection) {
            if ($reflection->isSubclassOf(Enum::class)) {
                $this->annotateClass($reflection->getName());
            }
        }
    }

    /**
     * Annotate a specific class by name
     *
     * @param string $className
     * @return void
     * @throws \ReflectionException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function annotateClass(string $className)
    {
        if (!is_subclass_of($className, Enum::class)) {
            $this->error("The given class must be an instance of BenSampo\Enum\Enum: $className.");
            return;
        }

        $reflection = new ReflectionClass($className);
        $this->annotate($reflection);
    }

    /**
     * Apply annotations to a reflected class
     *
     * @param ReflectionClass $reflectionClass
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function annotate(ReflectionClass $reflectionClass)
    {
        $docBlock = "/**\n";
        foreach ($reflectionClass->getConstants() as $name => $value) {
            $docBlock .= " * @method static static {$name}\n";
        }
        $docBlock .= " */\n";

        $shortName = $reflectionClass->getShortName();
        $fileName = $reflectionClass->getFileName();
        $contents = $this->filesystem->get($fileName);

        $classDeclaration = "class {$shortName}";

        if ($reflectionClass->isFinal()) {
            $classDeclaration = "final {$classDeclaration}";
        } elseif ($reflectionClass->isAbstract()) {
            $classDeclaration = "abstract {$classDeclaration}";
        }

        $classDeclarationOffset = strpos($contents, $classDeclaration);
        // Make sure we don't replace too much
        $contents = substr_replace(
            $contents,
            $docBlock . $classDeclaration,
            $classDeclarationOffset,
            strlen($classDeclaration)
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }

    /**
     * @return string
     */
    private function getScanPath(): string
    {
        if (!$this->option('folder')) {
            return app_path(self::DEFAULT_SCAN_FOLDER);
        }

        return $this->option('folder');
    }
}
