# 🎓 Frequência Certa

![Status](https://img.shields.io/badge/Status-Concluído-brightgreen)
![Laravel](https://img.shields.io/badge/Laravel-12+-FF2D20?style=flat&logo=laravel&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=flat&logo=pwa&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

> **Sistema de Gestão e Controle de Frequência Escolar.**

---

## 📖 Sobre o Projeto

O **Frequência Certa** é uma solução híbrida (Web e PWA) desenvolvida para empoderar estudantes do ensino médio e técnico no gerenciamento de sua assiduidade escolar.

O objetivo central é mitigar a evasão e a reprovação por faltas, além de auxiliar no monitoramento de requisitos para benefícios governamentais (como o **Pé-de-Meia**), que exigem frequência mínima de 80%. Através de uma interface intuitiva, o aluno consegue projetar seu desempenho e receber alertas visuais antes de atingir limites críticos.

### 🚀 Principais Funcionalidades

- **📱 Mobile First & PWA:** Aplicação instalável no celular (Android/iOS).
- **📅 Grade Dinâmica:** Montagem flexível de horários semanais adaptada à realidade do ensino técnico.
- **📊 Inteligência de Dados:** Projeção automática de dias letivos e cálculo percentual de presença em tempo real.
- **🚦 Alertas Semafóricos:** Indicadores visuais (Verde/Amarelo/Vermelho) que sinalizam o risco de reprovação por disciplina.
- **🗓️ Gestão de Eventos:** Cadastro de feriados e dias sem aula para garantir a precisão dos cálculos.
- **📄 Relatórios:** Geração de histórico de presença (Web).

---

## 📸 Demonstração

<div align="center">
  <img src="public/img/screenshots/desktop.png" alt="Dashboard Desktop" width="600">
  <br><br>
  <img src="public/img/screenshots/mobile.png" alt="Versão Mobile" width="250">
</div>

---

## 🛠️ Stack Tecnológica

O projeto utiliza uma arquitetura moderna focada em performance e experiência do usuário:

| Front-end & Interface | Back-end & Infraestrutura | Ferramentas de Dev |
| :--- | :--- | :--- |
| ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) **HTML5** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) **Laravel 12+** | ![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white) **Git** |
| ![Tailwind](https://img.shields.io/badge/Tailwind-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white) **Tailwind CSS** | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) **PHP 8.2** | ![VS Code](https://img.shields.io/badge/VS_Code-007ACC?style=flat-square&logo=visual-studio-code&logoColor=white) **VS Code** |
| ![AlpineJS](https://img.shields.io/badge/Alpine.js-8BC0D0?style=flat-square&logo=alpine.js&logoColor=black) **Alpine.js** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) **MySQL** | ![GitHub](https://img.shields.io/badge/GitHub-181717?style=flat-square&logo=github&logoColor=white) **GitHub** |
| ![PWA](https://img.shields.io/badge/PWA-Workbox-5A0FC8?style=flat-square&logo=pwa&logoColor=white) **Service Workers** | | |

---

## ✅ Pré-requisitos

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL (ou MariaDB)
---
## 👥 Equipe de Desenvolvimento

| Integrante | Funções Principais | GitHub |
| :--- | :--- | :--- |
| **Nicolas Viana Alves** | Full-Stack, Documentação & Deploy | [@naicolas-dev](https://github.com/naicolas-dev) |
| **Bruno Felix Seixas** | Front-end, PWA & UI/UX | [@obrunofelix](https://github.com/obrunofelix) |
| **Igor Thiago Costa Rodrigues** | Back-end & QA | [@luxxzvh](https://github.com/luxxzvh) |

---

## 🚀 Como rodar o projeto localmente

1. Clone o repositório:
```bash
git clone <URL_DO_REPOSITORIO>
cd <NOME_DO_PROJETO>
```
2. Instale as dependências do PHP:
```bash
composer install
```
3. Instale as dependências do Front-end:
```bash
npm install
```
4. Configure o arquivo .env:
```bash
cp .env.example .env
php artisan key:generate
```

5. Crie sua chave em: https://api.invertexto.com

Adicione no .env:
```env
INVERTEXTO_API_KEY=SUACHAVE_AQUI
```
6. Configure o banco de dados no .env e rode as migrations:
```bash
php artisan migrate
```
7. Inicie o servidor em dois terminais

**Terminal 1**
```bash
php artisan serve
```
**Terminal 2**
```bash
npm run dev
```
Acesse o projeto em: http://localhost:8000

---
## ⚠️ Disclaimer

Este sistema foi desenvolvido **exclusivamente para fins acadêmicos**, como parte de um Trabalho de Conclusão de Curso (TCC).

As informações de frequência apresentadas pelo sistema representam **estimativas baseadas na grade horária definida pelo estudante, nos registros de presença realizados e nos dias não letivos informados**, não devendo ser interpretadas como registros oficiais ou documentos válidos para comprovação institucional.

O projeto **não substitui** sistemas oficiais de controle escolar, diários de classe ou registros administrativos das instituições de ensino.



## 📄 Licença
Este projeto está sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.

---

<div align="center"> <sub>Projeto de caráter acadêmico e demonstrativo. <br>© Desenvolvido para o Curso Técnico em Desenvolvimento de Sistemas – 2025/2026.</sub> </div>
