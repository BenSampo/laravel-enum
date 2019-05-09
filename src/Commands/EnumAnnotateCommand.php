<?php

namespace BenSampo\Enum\Commands;

use ReflectionClass;
use BenSampo\Enum\Enum;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class EnumAnnotateCommand extends Command
{
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
    protected $description = 'Generate annotations for an enum class';

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $filesystem
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    protected function getArguments()
    {
        return [
            ['class', InputArgument::REQUIRED, 'The class name to generate annotations for']
        ];
    }

    /**
     * Handle the command call.
     *
     * @return void
     * @throws \ReflectionException
     */
    public function handle()
    {
        $className = $this->argument('class');

        if(! is_subclass_of($className, Enum::class)){
            $this->error("The given class must be an instance of BenSampo\Enum\Enum: $className.");
            return;
        }

        $reflection = new ReflectionClass($className);

        $docBlock = "/**\n";
        foreach($reflection->getConstants() as $name => $value) {
            $docBlock .= " * @method static static {$name}\n";
        }
        $docBlock .= " */\n";

        $shortName = $reflection->getShortName();
        $fileName = '/' . $reflection->getFileName();
        $contents = $this->filesystem->get($fileName);

        $classDeclaration = "class {$shortName}";
        $classDeclarationOffset = strpos($contents, $classDeclaration);
        // Make sure we don't replace too much
        $contents = substr_replace(
            $contents,
            "{$docBlock}\nclass {$shortName}",
            $classDeclarationOffset,
            strlen($classDeclaration)
        );

        $this->filesystem->put($fileName, $contents);
        $this->info("Wrote new phpDocBlock to {$fileName}.");
    }
}
