# Protótipo de gestão de água

Aplicação de demonstração em Laravel para gerir facturas e pagamentos de água com dados simulados. O desenvolvimento segue fases; as Fases 1 e 2 estão implementadas neste momento.

## Fase 1 — autenticação e perfis

- Login e logout usam a autenticação de sessão do Laravel, validação por Form Request, CSRF, limitação de tentativas e regeneração da sessão após login.
- Contas inactivas não iniciam sessão; uma sessão existente também é terminada quando a conta fica inactiva.
- Os perfis `admin`, `agente` e `utente` são controlados por middleware. A Policy de `User` permite a um utilizador consultar apenas a própria conta; as operações de gestão ficam reservadas ao administrador.
- O administrador pode criar contas e activar/desactivar utilizadores; a última conta administrativa activa e a própria conta não podem ser desactivadas.
- O modelo usa a tabela `utilizadores` e os campos de identidade e perfil definidos para o projecto. A migração converte a tabela inicial do Laravel para esse esquema.
- Não existe registo público. A conta administrativa de demonstração é criada pelo seeder.

## Fase 2 — utentes, contadores e tarifas

- O administrador pode registar e actualizar utentes e contadores; em vez de apagar registos, pode marcá-los como inactivos para preservar as referências.
- Um utente pode ter uma conta de portal opcional. A ligação só aceita contas do perfil `utente` e a relação é única.
- As tarifas são globais (o modelo não define categorias) e cada data de vigência é única para evitar selecção ambígua. Uma nova tarifa representa uma alteração futura; os registos não são apagados nem editados para preservar o histórico.
- Os estados de utentes e contadores são `activo` ou `inactivo`.

## Fase 3 — leituras e cálculo do consumo

- Agentes e administradores podem registar leituras; o sistema deriva a anterior da última actual do contador, calcula o consumo e rejeita leituras inferiores ou datas fora da sequência.
- A primeira leitura usa anterior `0`, conforme decisão para este protótipo. Leituras não podem anteceder a instalação do contador, ser futuras ou repetir/preceder a última data registada.
- A leitura e a factura são persistidas na mesma transacção. A factura usa a tarifa mais recente cuja vigência começou até à data da leitura; a emissão ocorre na data actual do sistema e o vencimento é calculado a partir da emissão e do prazo dessa tarifa.
- Não há criação manual de facturas nem endpoints de edição/eliminação de leituras ou facturas. Utentes só podem consultar facturas associadas à sua conta; agentes consultam facturas e leituras.

### Executar localmente

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Conta local para demonstração: `test@example.com` / `password`. É uma credencial exclusivamente simulada para este protótipo; não a reutilize noutros ambientes.

Pagamentos, multas, PDF e relatórios ainda não estão implementados. A aplicação não deve ser implantada para uso empresarial.
