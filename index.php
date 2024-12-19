<?php

use GabineteDigital\Controllers\UsuarioController;

require 'vendor/autoload.php';


$usuarioController = new UsuarioController();



                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar'])) {
                        $usuario = [
                            'usuario_nome' => htmlspecialchars($_POST['usuario_nome'], ENT_QUOTES, 'UTF-8'),
                            'usuario_email' => htmlspecialchars($_POST['usuario_email'], ENT_QUOTES, 'UTF-8'),
                            'usuario_telefone' => htmlspecialchars($_POST['usuario_telefone'], ENT_QUOTES, 'UTF-8'),
                            'usuario_aniversario' => "2000-" . implode('-', array_reverse(explode('/', htmlspecialchars($_POST['usuario_aniversario'], ENT_QUOTES, 'UTF-8')))),
                            'usuario_ativo' => htmlspecialchars($_POST['usuario_ativo'], ENT_QUOTES, 'UTF-8'),
                            'usuario_nivel' => htmlspecialchars($_POST['usuario_nivel'], ENT_QUOTES, 'UTF-8'),
                            'usuario_senha' => htmlspecialchars($_POST['usuario_senha'], ENT_QUOTES, 'UTF-8'),
                            'usuario_cliente' => '7f5463c4-be22-11ef-be69-f3d75f5485a2',
                            'foto' => $_FILES['usuario_foto']
                        ];

                        $result = $usuarioController->criarUsuario($usuario);

                        print_r($result);
                        
                    }

                    

                    ?>

                    <form class="row g-2 form_custom" id="form_novo" method="POST" enctype="multipart/form-data">
                        <div class="col-md-6 col-12">
                            <input type="text" class="form-control form-control-sm" name="usuario_nome" placeholder="Nome" value="sfsdfs" required>
                        </div>
                        <div class="col-md-2 col-12">
                            <input type="email" class="form-control form-control-sm" name="usuario_email" placeholder="Email"  value="dsfdsds@dsds.com">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_telefone" placeholder="Celular (com DDD)" data-mask="(00) 00000-0000" value="3423423" maxlength="15" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="text" class="form-control form-control-sm" name="usuario_aniversario" data-mask="00/00" value="31/03" placeholder="Aniversário (dd/mm)" required>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_ativo" required>
                                <option value="1" selected>Ativado</option>
                                <option value="0">Desativado</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-6">
                            <select class="form-select form-select-sm" name="usuario_nivel" required>
                                <option value="1">Administrador</option>
                                <option value="2" selected>Assessor</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="password" class="form-control form-control-sm" id="usuario_senha" name="usuario_senha" value="1" placeholder="Senha" required>
                        </div>
                        
                        <div class="col-md-3 col-12">
                                <input type="file" name="usuario_foto" />
                                <button type="submit" class="btn btn-success btn-sm" name="btn_salvar"><i class="bi bi-floppy-fill"></i> Salvar</button>
                        </div>
                    </form>