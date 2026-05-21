Aluno: Pedro Henrique dos Santos Souza
Aluno: Francisco de Assis Branco Filho

# 🎓 Sistema de Médias Escolares

Sistema completo para cadastro de turmas, alunos, lançamento de notas e cálculo de média com conceitos e recuperação.

## Funcionalidades

- Cadastro de Turmas (com opção de fechar)
- Cadastro de Alunos por turma
- Lançamento das 4 notas bimestrais
- Cálculo automático de média e conceito (A, B, C, D)
- Mensagens motivacionais personalizadas
- Sistema de Recuperação (soma com média ≥ 10 = aprovado)
- Turmas fechadas não permitem mais alterações
- Interface moderna com Bootstrap 5

## Tecnologias

- PHP 8+
- MySQL
- Bootstrap 5
- PDO

## Estrutura de Diretórios
/sistema-medias/
├── index.php
├── cadastro-turma.php
├── cadastro-aluno.php
├── lancar-notas.php
├── ver-turma.php
├── fechar-turma.php
├── config/
│   └── db.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
└── README.md

## Usuário Padrão

Não possui login (projeto simples). Recomenda-se adicionar autenticação em produção.



**Desenvolvido para fins educacionais**
