<?php
namespace DTLaravelEloquent\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class RDTInstallCommand extends Command
{
    protected $signature = 'RDT:install {--raw : Copiar archivos SCSS y JS sin transpilar}';
    protected $description = 'Instala los archivos necesarios para DataTable Laravel Eloquent de REP98';

    public function handle()
    {
        $this->info('Instalando RDT...');

        if ($this->option('raw')) {
            $this->copyRawAssets();
            $this->installNpmDependencies();
        } else {
            $this->publishCompiledAssets();
        }

        $this->info('RDT instalado correctamente.');
    }

    protected function copyRawAssets()
    {
        $path = dirname(__DIR__);
        $assets = [
            'js' => $path . "/resources/js",
            'sass' => $path . "/resources/scss"
        ];

        foreach ($assets as $type => $sourcePath) {
            $destinationPath = resource_path($type);

            if (!File::exists($sourcePath)) {
                $this->error("El directorio $sourcePath no existe.");
                return;
            }

            File::ensureDirectoryExists($destinationPath);

            $files = File::allFiles($sourcePath);
            foreach ($files as $file) {
                File::copy($file->getPathname(), $destinationPath . '/' . $file->getFilename());
            }
        }

        $this->info('Archivos SCSS y JS sin transpilar copiados correctamente.');
    }


    protected function publishCompiledAssets()
    {
        $this->call('vendor:publish', [
            '--tag' => 'public',
            '--force' => true,
        ]);

        $this->info('Archivos compilados publicados correctamente.');
    }

    protected function installNpmDependencies()
    {
        $this->info('Instalando dependencias npm...');

        $process = new Process(['npm', 'install', 'bootstrap', '@rep985/fascinots', 'simple-datatables']);
        $process->setWorkingDirectory(base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Error al instalar las dependencias npm.');
            return;
        }

        $this->info('Dependencias npm instaladas correctamente.');
    }
   
}
