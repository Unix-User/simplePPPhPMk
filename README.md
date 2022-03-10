# simplePPPhPMk

Painel de controle de usúarios PPP em dispositivos mikrotik/routeros. Registre dispositivos mikrotik/routeros para gerenciamento de usuários PPP local ou em nuvem, feito em PHP.

Cadastre automaticamente usuários e seus perfis em <a href='https://unixlocal.ml'>site</a> para sincronização com servidor PPP de dispositivos mikrotik/routeros, com o clique de um botão e recupere os registros no banco de dados.

![gif1](https://user-images.githubusercontent.com/38821945/157555687-712ad725-e2a4-48c3-86f4-3ad04ef49f19.gif)

Obtenha em tempo real dados e informações de recursos dos dispositivos cadastrados ou usuários conectados, podendo desconectar, habilitar e apagar cadastros de usuários(novos usuários e perfis de conexão são automaticamente adicionados pela função sincronizar e obtidos do <a href='https://unixlocal.ml'>site</a> ou criar, modificar, atualizar e deletar novos dispositivos;

![gif21](https://user-images.githubusercontent.com/38821945/157559421-937dcf23-7758-4430-905e-ef327f34ec2a.gif)


Requisitos:

- Apache
- PHP(testado em PHP 7.4.3)
- Composer

Recomendados:
VPN lt2p/ipsec pré-instalada: https://github.com/hwdsl2/setup-ipsec-vpn


Instalação:

- clone esse repositório, e rode composer update dentro da pasta do projeto
  ```
  git clone https://github.com/Unix-User/simplePPPhPMk.git
  cd simplePPPhPMk/
  composer update
  ```
- edite o arquivo /etc/sudoers e adicione as permissões para o usuário executar o script da vpn
    ```
    ~$ sudo nano /etc/sudoers
    ```
    Voce deve adicionar permissões customizadas para segurança de seu servidor, para fins de praticidade e testes, adicione as seguintes permissões ao arquivo(lembre-se de altera-las posteriormente)
    ```
    ## adicionar as permissões abaixo permitem que o servidor HTTP execute qualquer comando sudo
    www-data        ALL=(ALL) NOPASSWD:/usr/bin
    ```
Em breve
- [ ] Melhorias no modulo vpn-ikev2 (método de acesso direto sem shell scripts)
- [ ] Sistema de agendamento com Schedulle ou Cron
- [ ] Mesclar projeto com o https://github.com/Unix-User/MP_unixlocal para uma aplicação mais completa :tada:
