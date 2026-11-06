<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CriarAcessoAdministrativo extends Command
{
    protected $signature = 'vetflow:criar-acesso
                            {--papel=admin : admin ou investidor}';

    protected $description = 'Cria uma conta administrativa de forma controlada';

    public function handle(): int
    {
        $papel = (string) $this->option('papel');

        if (! in_array($papel, [
            User::PAPEL_ADMIN,
            User::PAPEL_INVESTIDOR,
        ], true)) {
            $this->error('Papel inválido.');

            return self::FAILURE;
        }

        // Confirmação extra em produção.
        if (
            app()->environment('production') &&
            ! $this->confirm('Criar conta no banco de produção?')
        ) {
            return self::FAILURE;
        }

        $nome = trim((string) $this->ask('Nome da conta'));
        $email = strtolower(
            trim((string) $this->ask('E-mail de acesso'))
        );

        // A senha não aparece no terminal.
        $senha = (string) $this->secret('Senha de acesso');
        $confirmacao = (string) $this->secret('Confirme a senha');

        $validador = Validator::make([
            'name' => $nome,
            'email' => $email,
            'password' => $senha,
            'password_confirmation' => $confirmacao,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if ($validador->fails()) {
            foreach ($validador->errors()->all() as $erro) {
                $this->error($erro);
            }

            return self::FAILURE;
        }

        DB::transaction(function () use (
            $nome,
            $email,
            $senha,
            $papel
        ) {
            $usuario = User::create([
                'name' => $nome,
                'email' => $email,
                'password' => $senha,
            ]);

            // Esses campos não são preenchíveis em massa.
            $usuario->papel = $papel;
            $usuario->ativo = true;
            $usuario->save();
        });

        $this->info('Conta administrativa criada com sucesso!');

        return self::SUCCESS;
    }
}
