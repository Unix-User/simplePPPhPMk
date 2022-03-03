# simplePPPhPMk

Painel de controle de usúarios PPP em dispositivos mikrotik/routeros. Registre dispositivos mikrotik/routeros para gerenciamento local ou em nuvem, feito em PHP.

Cadastre automaticamente usuários e seus perfis em servidor PPP de dispositivos mikrotik/routeros, com o clique de um botão e recupere por meio de um fetch no <a href='https://unixlocal.ml'>site</a>, registros no banco de dados.

Obtenha em tempo real dados e informações de recursos dos dispositivos cadastrados e usuários conectados, podendo desconectar, habilitar e apagar cadastros de usuários(novos cadastrados são automaticamente adicionados pela função sincronizar e obtidos do <a href='https://unixlocal.ml'>site</a>

Instalação:

clone esse repositório, e rode composer update dentro da pasta do projeto

Requisitos:
VPN lt2p/ipsec pré-instalada: https://github.com/hwdsl2/setup-ipsec-vpn
