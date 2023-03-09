//Atualizado em 05/04/2022

function TamanhoIframe()
{
	var w = window.innerWidth
			|| document.documentElement.clientWidth
			|| document.body.clientWidth;
			
	var h = window.innerHeight
			|| document.documentElement.clientHeight
			|| document.body.clientHeight;
			
	document.getElementById("pagina").style.height = (h - 90) + 'px';
}

function AbreFechaNivel(btn, nivel)
{
	if (btn.innerHTML == '-')
	{
		btn.innerHTML = '+';
		document.getElementById(nivel).style.display = 'none';
	}
	else
	{
		btn.innerHTML = '-';
		document.getElementById(nivel).style.display = '';
	}
}

function Maiusculo(txtbox)
{
	txtbox.value = txtbox.value.toUpperCase();
}

function Numerico(txtbox)
{
	var cnes = txtbox.value;
	var numeros = /[^0-9]/mi;
	var res = cnes.match(numeros);
	txtbox.value = cnes.replace(res,"");
}

function FormataData(txtbox)
{
	var txtValor = txtbox.value;
	var numeros = /[^0-9|/]/mi;
	var res = txtValor.match(numeros);
	txtbox.value = txtValor.replace(res,"");

	var tam = txtbox.value.length;

	for (var i = 1 ; i <= tam ; i++)
	{
		var dataDig = txtbox.value.substring(0, i);

		switch (i)
		{
			case 1:
				if (dataDig * 1 > 3)
				{
					txtbox.value = '';
				}
				break;
			case 2:
				if (dataDig * 1 > 31)
				{
					txtbox.value = txtbox.value.substring(0, i-1);
				}
				break;
			case 3:
				if (dataDig.indexOf('/') != 2)
				{
					txtbox.value = txtbox.value.substring(0, i-1);
				}
				break;
			case 4:
				var mes = dataDig.split('/')[1];

				if (mes > 1)
				{
					txtbox.value = txtbox.value.substring(0, i-1);
				}
				break;
			case 5:
				var dia = dataDig.split('/')[0];
				var mes = dataDig.split('/')[1];

				if ((mes > 12) || (dia > 29 && mes == 02) || (dia == 31 && (mes == 04 || mes == 06 || mes == 09 || mes == 11)))
				{
					txtbox.value = txtbox.value.substring(0, i-1);
				}
				break;						
		}					
	}

	if ((txtbox.value.length == 2) || (txtbox.value.length == 5))
	{
		txtbox.value = txtbox.value + '/';
	}
	
	if ((txtbox.value.indexOf('//') > -1) || (txtbox.value.indexOf('00/') > -1) || (txtbox.value.indexOf('/0000') > -1))
	{
		txtbox.value = '';
	}
}

function FormataCep(txtbox)
{
	var txtValor = txtbox.value;
	var numeros = /[^0-9|-]/mi;
	var res = txtValor.match(numeros);
	txtbox.value = txtValor.replace(res,"");

	var tam = txtbox.value.length;

	if (tam == 5)
	{
		txtbox.value = txtbox.value + '-';
	}

	if ((txtbox.value.lastIndexOf('-') > -1) &&  (txtbox.value.lastIndexOf('-') != 5))
	{
		txtbox.value = '';
	}
}

function validaCNS(cns)
{
	let cnsValido = false;

	if (cns.trim().length == 15) 
	{
		let soma = 0;
		let resto = 0;

		switch (cns.substr(0, 1))
		{
			case '1':
			case '2':
				let pis = cns.substr(0, 11);

				soma = (((pis.substr(0, 1) * 1) * 15) +
						((pis.substr(1, 1) * 1) * 14) +
						((pis.substr(2, 1) * 1) * 13) +
						((pis.substr(3, 1) * 1) * 12) +
						((pis.substr(4, 1) * 1) * 11) +
						((pis.substr(5, 1) * 1) * 10) +
						((pis.substr(6, 1) * 1) * 9) +
						((pis.substr(7, 1) * 1) * 8) +
						((pis.substr(8, 1) * 1) * 7) +
						((pis.substr(9, 1) * 1) * 6) +
						((pis.substr(10, 1) * 1) * 5));
						
				resto = soma % 11;
				let dv = 11 - resto;
				let resultado = 0;
		
				if (dv == 11) 
				{ 
					dv = 0;	
				}

				if (dv == 10) 
				{ 
					soma = ((((pis.substr(0, 1) * 1) * 15) +
							((pis.substr(1, 1) * 1) * 14) +
							((pis.substr(2, 1) * 1) * 13) +
							((pis.substr(3, 1) * 1) * 12) +
							((pis.substr(4, 1) * 1) * 11) +
							((pis.substr(5, 1) * 1) * 10) +
							((pis.substr(6, 1) * 1) * 9) +
							((pis.substr(7, 1) * 1) * 8) +
							((pis.substr(8, 1) * 1) * 7) +
							((pis.substr(9, 1) * 1) * 6) +
							((pis.substr(10, 1) * 1) * 5)) + 2);
							
					resto = soma % 11;
					dv = 11 - resto;
					resultado = pis + "001" + dv;
				}
				else 
				{
					resultado = pis + "000" + dv;
				}
		
				if (cns == resultado)
				{
					cnsValido = true;
				}

				break;
			case '7':
			case '8':
			case '9':
				soma = (((cns.substr(0, 1)) * 15) +
						((cns.substr(1, 1)) * 14) +
						((cns.substr(2, 1)) * 13) +
						((cns.substr(3, 1)) * 12) +
						((cns.substr(4, 1)) * 11) +
						((cns.substr(5, 1)) * 10) +
						((cns.substr(6, 1)) * 9) +
						((cns.substr(7, 1)) * 8) +
						((cns.substr(8, 1)) * 7) +
						((cns.substr(9, 1)) * 6) +
						((cns.substr(10, 1)) * 5) +
						((cns.substr(11, 1)) * 4) +
						((cns.substr(12, 1)) * 3) +
						((cns.substr(13, 1)) * 2) +
						((cns.substr(14, 1)) * 1));

				resto = soma % 11;
				console.log(resto);
				
				if (resto == 0)
				{
					cnsValido = true;
				}
				
				break;
		}
	}

	return cnsValido;
}

function validaCPF(cpf)
{
	if (cpf.length != 11)
	{
		return false;
	}
	else
	{
		var s = 0;

		for (var i = 1 ; i < cpf.length ; i++)
		{
			if (cpf.charAt(0) == cpf.charAt(i))
			{
				s = s + 1;
			}
		}

		if (s == 10)
		{
			return false;
		}
		else
		{
			var num = 2;
			var inicio = 10;

			for (var t = 1 ; t <= 2 ; t++)
			{
				var soma = 0;

				for (var n = 0 ; n < (cpf.length - num) ; n++)
				{
					soma = soma + (cpf.charAt(n) * inicio);				
					inicio = inicio - 1;
				}

				var resto = (soma * 10) % 11;

				if (resto == 10)
				{
					resto = 0;
				}

				if (resto - parseInt(cpf.charAt(cpf.length - num)) == 0)
				{
					num = 1;
					inicio = 11;
				}
				else
				{
					return false;
					break;
				}
			}
		}
	}

	return true;
}

function IniciaTemporizador(tempoRep, relogio)
{
	try
	{
		if (relogio == 'idTempo')
		{
			ChamaProximoConsultorioRecepcao();
		}
		else if (relogio == 'idTempoHist')
		{
			ChamaProximo('AUTOMATICO');
		}
		else if (relogio == 'idTempoTriagem')
		{
			ChamaProximoTriagem('AUTOMATICO');
		}

		const tempo = setInterval(function(){ AtualizaTemporizador(tempoRep, relogio); }, 1000);
	}
	catch (erro)
	{
		console.log(erro.message);
	}
}

function AtualizaTemporizador(tempoRep, relogio)
{
	let temporizador = document.getElementById(relogio);
	
	let tempo = temporizador.innerHTML.split(":");
	let min = tempo[0] * 1;
	let seg = tempo[1] * 1;

	if (seg > 0)
	{
		seg = seg - 1;
	}
	else
	{
		if (min > 0)
		{
			min = min - 1;
			seg = 59;
		}
		else
		{
			min = tempoRep;

			if (relogio == 'idTempo')
			{
				ChamaProximoConsultorioRecepcao();
			}
			else if (relogio == 'idTempoHist')
			{
				ChamaProximo('AUTOMATICO');
			}
			else if (relogio == 'idTempoTriagem')
			{
				ChamaProximoTriagem('AUTOMATICO');
			}
		}
	}

	seg = seg < 10 ? '0' + seg : seg;
	min = min < 10 ? '0' + min : min;

	temporizador.innerHTML = min + ':' + seg;
}

function IncluiDescricaoClinica(situacao, dataHora, pressao, hgt, descricao, idHist)
{
	let modelo = "<div class='divDadosHistClinica' id='ID_HIST'>";
	modelo = modelo + "<div><strong>Situação:</strong> SITUACAO_INFO</div>";
	modelo = modelo + "<div><strong>Data/hora:</strong> DATA_HORA_INFO</div>";
	modelo = modelo + "<div>";
	modelo = modelo + "<div style=\"width: 50%; float:left;\" id='PRESSAO_ID'><strong>Pressão:</strong> PRESSAO_INFO</div>";
	modelo = modelo + "<div style=\"width: 50%; float:left;\" id='HGT_ID'><strong>HGT:</strong> HGT_INFO</div>";
	modelo = modelo + "</div>";
	modelo = modelo + "<div id='DESCRICAO_ID'><strong>Descrição:</strong> DESCRICAO_INFO</div>";
	modelo = modelo + "</div>";

	modelo = modelo.replace("ID_HIST", 'HIST' + idHist);
	modelo = modelo.replace("PRESSAO_ID", 'PRESSAO' + idHist);
	modelo = modelo.replace("HGT_ID", 'HGT' + idHist);
	modelo = modelo.replace("DESCRICAO_ID", 'DESCRICAO' + idHist);
	modelo = modelo.replace("SITUACAO_INFO", situacao);
	modelo = modelo.replace("DATA_HORA_INFO", dataHora);
	modelo = modelo.replace("PRESSAO_INFO", pressao);
	modelo = modelo.replace("HGT_INFO", hgt);
	modelo = modelo.replace("DESCRICAO_INFO", descricao);

	return modelo;
}

function ChamaProximo(tipo)
{
	try
	{
		let cnsProf = document.getElementById('cnsProf').value;
		let conteudo = document.getElementById('conteudohistClinica');
		
		conteudo.innerHTML = "<div class='divTituloHistClinica'>História Clínica</div>";
		document.getElementById('cnsUsuario').value = '';
		document.getElementById('nomeUsuario').value = '';
		document.getElementById('listaRisco').value = 'selecione';

		var xhttp;
		xhttp = new XMLHttpRequest();
		
		xhttp.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200)
			{				
				let dadosRes = JSON.parse(this.responseText);
				//alert(JSON.stringify(dadosRes));
				
				if (dadosRes.length > 0)
				{
					for (let i = 0 ; i < dadosRes.length ; i++)
					{
						if (dadosRes[i]['usuario'])
						{
							let cnsUsuario = dadosRes[i]['usuario'].cnsUsuario;
							let nomeUsuario = dadosRes[i]['usuario'].nomeUsuario;
							
							document.getElementById('cnsUsuario').value = cnsUsuario;
							document.getElementById('nomeUsuario').value = nomeUsuario;
						}
						else if (dadosRes[i]['entrada'])
						{
							let classificacao = dadosRes[i]['entrada'].classificacao;
							let idEnt = dadosRes[i]['entrada'].idEnt;
	
							document.getElementById('listaRisco').value = classificacao;
							document.getElementById('descricao').value = '';

							document.getElementById('idEnt').value = idEnt;
						}
						else if (dadosRes[i]['historia'])
						{
							for (let h = 0 ; h < dadosRes[i]['historia'].length ; h++)
							{
								let idHc = dadosRes[i]['historia'][h].idHc;
								let situacao = dadosRes[i]['historia'][h].situacao;
								let dataHora = dadosRes[i]['historia'][h].dataHora;
								
								let pressao = !dadosRes[i]['historia'][h].pressao ? '---' : dadosRes[i]['historia'][h].pressao;
								let hgt = !dadosRes[i]['historia'][h].hgt ? '---' : dadosRes[i]['historia'][h].hgt;
								let descricao = !dadosRes[i]['historia'][h].historiaClinica ? '---' : dadosRes[i]['historia'][h].historiaClinica;
		
								let histClinica = IncluiDescricaoClinica(situacao, dataHora, pressao, hgt, descricao, idHc);
		
								conteudo.innerHTML = conteudo.innerHTML + histClinica;

								if (h == 0)
								{
									document.getElementById('idHc').value = idHc;
								}
							}
						}
					}
				}
				else
				{
					switch (tipo)
					{
						case 'MANUAL':
							alert('Nenhum usuário em espera ou o prosissional não está vinculado a um consultório!');
						case 'AUTOMATICO':
							ExibeUltimaModificacao('ATENDIMENTO');
							break;
					}
				}
			}
		};
		
		xhttp.open("POST", "res_ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("cns=" + cnsProf + "&funcao=5" + "&sss=" + SSS);
	}
	catch (erro)
	{
		alert(erro.message);
	}
}

function ChamaProximoTriagem(tipo)
{
	try
	{
		let cnsProf = document.getElementById('cnsProf').value;
		
		document.getElementById('listaRisco').value = 'selecione';

		var xhttp;
		xhttp = new XMLHttpRequest();
		
		xhttp.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200)
			{
				let dadosRes = '';

				if (this.responseText.length > 0)
				{
					dadosRes = JSON.parse(this.responseText);
				}
				
				//alert(JSON.stringify(dadosRes));
				if ((dadosRes.length > 0) && (dadosRes[0]['usuario'].cnsUsuario != null))
				{
					if (dadosRes[0]['usuario'].cnsUsuario != null)
					{
						let cnsUsuario = dadosRes[0]['usuario'].cnsUsuario;
						let nomeUsuario = dadosRes[0]['usuario'].nomeUsuario;
						let cpfUsuario = dadosRes[0]['usuario'].cpfUsuario;
						cpfUsuario = cpfUsuario == null ? '' : cpfUsuario;

						let nomeMae = dadosRes[0]['usuario'].nomeMae;
						let dn = dadosRes[0]['usuario'].dataNasc;
						let sexo = dadosRes[0]['usuario'].sexo;
						sexo = sexo == null ? 'selecione' : sexo;

						let cep = dadosRes[0]['usuario']['endereco'].cep;
						let logr = dadosRes[0]['usuario']['endereco'].logradouro;
						let compl = dadosRes[0]['usuario']['endereco'].compl;
						let num = dadosRes[0]['usuario']['endereco'].numero;
						let bairro = dadosRes[0]['usuario']['endereco'].bairro;
						let municipio = dadosRes[0]['usuario']['endereco'].municipio;
						let estado = dadosRes[0]['usuario']['endereco'].estado;
						estado = estado == null ? 'selecione' : estado;
						
						if (document.getElementById('cns').value == '')
						{
							document.getElementById('cns').value = cnsUsuario;
							document.getElementById('nome').value = nomeUsuario;
							document.getElementById('cpf').value = cpfUsuario;
							document.getElementById('nomeMae').value = nomeMae;
							document.getElementById('nasc').value = dn;
							document.getElementById('sexo').value = sexo;
							document.getElementById('cep').value = cep;
							document.getElementById('logr').value = logr;
							document.getElementById('compl').value = compl;
							document.getElementById('num').value = num;
							document.getElementById('bairro').value = bairro;
							document.getElementById('municipio').value = municipio;
							document.getElementById('estado').value = estado;
						}
						else
						{
							ExibeUltimaModificacao('TRIAGEM');
						}

						document.getElementById('cns').disabled = true;
					}
					if (dadosRes[1]['entrada'].idEnt != null)
					{
						let classificacao = dadosRes[1]['entrada'].classificacao;
						classificacao = classificacao == '---' ? 'selecione' : classificacao;
						
						let situacao = dadosRes[1]['entrada'].situacao;
						let idEnt = dadosRes[1]['entrada'].idEnt;

						if (document.getElementById('idEnt').value == '')
						{
							document.getElementById('listaRisco').value = classificacao;
						}

						if (situacao == '---')
						{
							document.getElementById('situacao').value = 'Triagem';
						}

						document.getElementById('idEnt').value = idEnt;
						document.getElementById("tipoEntrada").value = 'AUTOMATICO';
					}
				}
				else
				{
					switch (tipo)
					{
						case 'MANUAL':
							alert('Nenhum usuário em espera para triagem!');
						case 'AUTOMATICO':
							ExibeUltimaModificacao('TRIAGEM');
							document.getElementById("tipoEntrada").value = '';
							break;
					}
				}
			}
		};
		
		xhttp.open("POST", "res_ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("cns=" + cnsProf + "&funcao=8" + "&sss=" + SSS);
	}
	catch (erro)
	{
		alert(erro.message);
	}
}

function ExibeUltimaModificacao(perfil)
{
	const d = new Date();
	let h = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
	let m = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
	let s = d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds();
	let tempo = '(' + h + ':' + m + ':' + s + ')';

	if (perfil == 'TRIAGEM')
	{
		document.getElementById('idTempoTriagemAtlz').innerHTML = tempo;
	}
	else if (perfil == 'ATENDIMENTO')
	{
		document.getElementById('idTempoHistAtlz').innerHTML = tempo;
	}
}

function AtualizaDadosClinicos(cxtxt, tipo)
{
	switch (tipo)
	{
		case 'PRESSAO':
			Maiusculo(cxtxt);
			break;
		case 'HGT':
			Numerico(cxtxt);
			break;
		case 'DESCRICAO':
			break;
		default:
			break;
	}

	let historias = document.getElementsByClassName('divDadosHistClinica');

	if (historias.length > 0)
	{
		let idHist = historias[0].getAttribute('id').replace('HIST', '');

		let novoValor = cxtxt.value == '' ? '---' : cxtxt.value; 
		let legendaValor = document.getElementById(tipo + idHist).innerText.split(':');
		
		document.getElementById(tipo + idHist).innerHTML = '<strong>' + legendaValor[0] + ':</strong> ' + novoValor;
	}	
}

function ChamaProximoConsultorioRecepcao()
{
	try
	{
		let divChamado = document.getElementById('idChamado');
		
		let colunas = "<div class='linChamado'>"
		colunas = colunas + "<div class='colChamado' style=\"width: 5%;\">Consultório</div>";
		colunas = colunas + "<div class='colChamado' style=\"width: 20%;\">Tipo</div>";
		colunas = colunas + "<div class='colChamado' style=\"width: 5%;\">Classificação</div>";
		colunas = colunas + "<div class='colChamado' style=\"width: 70%;\">Cidadão</div>";
		colunas = colunas + "</div>";
		
		divChamado.innerHTML = colunas;

		var xhttp;
		xhttp = new XMLHttpRequest();
		
		xhttp.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200)
			{
				//alert(this.responseText);
				let dadosRes = JSON.parse(this.responseText);
				//alert(JSON.stringify(dadosRes));
				if (dadosRes.length > 0)
				{
					for (let i = 0 ; i < dadosRes.length ; i++)
					{
						if (dadosRes[i]['consultorios'])
						{
							let idSala = dadosRes[i]['consultorios'].idSala;
							let numSala = dadosRes[i]['consultorios'].numSala;
							numSala = numSala < 10 ? '0' + numSala : numSala;

							let perfil = dadosRes[i]['consultorios'].perfil;

							let cnsUsuario = dadosRes[i]['consultorios'].cnsUsuario;
							let nomeUsuario = dadosRes[i]['consultorios'].nomeUsuario;
							let cidadao = cnsUsuario + ' - ' + nomeUsuario;

							let classificacao = dadosRes[i]['consultorios'].classificacao;
							let idEnt = dadosRes[i]['consultorios'].idEnt;
							let situacao = dadosRes[i]['consultorios'].situacao;

							let chamado = "<div class='linChamado'>"
							chamado = chamado + "<div class='colChamado' style=\"width: 5%;\">" + numSala + "</div>";
							chamado = chamado + "<div class='colChamado' style=\"width: 20%;\">" + perfil + "</div>";
							chamado = chamado + "<div class='colChamado' style=\"width: 5%;\">" + classificacao + "</div>";
							chamado = chamado + "<div class='colChamado' style=\"width: 70%;\">" + cidadao + "</div>";
							chamado = chamado + "</div>";

							divChamado.innerHTML = divChamado.innerHTML + chamado;
						}
					}
				}
			}
		};
		
		xhttp.open("POST", "res_ajax.php", true);
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("logado=" + 'NAO' + "&funcao=7" + "&sss=" + SSS);
	}
	catch (erro)
	{
		console.log(erro.message);
	}
}

function AdicionaSituacoes(idSel, arraySituacoes)
{
	var select = document.getElementById(idSel);
	select.innerHTML = '';

	var op = document.createElement("option");
	
	for (let i = 0 ; i < arraySituacoes.length ; i++)
	{
		let situacao = arraySituacoes[i];

		op.text = situacao;
		select.add(op);
	}
}

function SiglaNomeEstado(uf, tipo)
{
	let resposta = '';

	const estados = { 	'AC' : 'Acre',
						'AL' : 'Alagoas',
						'AP' : 'Amapá',
						'AM' : 'Amazonas',
						'BA' : 'Bahia',
						'CE' : 'Ceará',
						'DF' : 'Distrito Federal',
						'ES' : 'Espírito Santo',
						'GO' : 'Goiás',
						'MA' : 'Maranhão',
						'MT' : 'Mato Grosso',
						'MS' : 'Mato Grosso do Sul',
						'MG' : 'Minas Gerais',
						'PA' : 'Pará',
						'PB' : 'Paraíba',
						'PR' : 'Paraná',
						'PE' : 'Pernambuco',
						'PI' : 'Piauí',
						'RJ' : 'Rio de Janeiro',
						'RN' : 'Rio Grande do Norte',
						'RS' : 'Rio Grande do Sul',
						'RO' : 'Rondônia',
						'RR' : 'Roraima',
						'SC' : 'Santa Catarina',
						'SP' : 'São Paulo',
						'SE' : 'Sergipe',
						'TO' : 'Tocantins'
					};

	for (let chave in estados)
	{
		if (tipo == 'RETORNA_SIGLA')
		{
			if (estados[chave] == uf)
			{
				resposta = chave;
			}
		}
		else if (tipo == 'RETORNA_NOME')
		{
			if (chave == uf)
			{
				resposta = estados[chave];
			}
		}
	}

	return resposta;
}

function abreCalendario(btn)
{
	try
	{
		const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
		const diasSemana = ['Do', 'Se', 'Te', 'Qu', 'Qu', 'Se', 'Sa'];
		const diasMeses = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

		let dia = 0;
		let hoje = new Date();
		let anoAtual = hoje.getFullYear();
		let mesAtual = hoje.getMonth();

		let calendario = "<div style=\"width: 50px; heigth: 50px; display: block; padding: 5px;\">";
		calendario = calendario + "<div>";
		
		calendario = calendario + "</div>";

		calendario = calendario + "<div>";
		calendario = calendario + "<table>";
        
        calendario = calendario + "<tr>";
        calendario = calendario + "<td>";
        calendario = calendario + "<button type='button'><<</button>";
        calendario = calendario + "</td>";
        calendario = calendario + "<td colspan='5'>";
        calendario = calendario + "<select>";
		for (let i = 0 ; i < meses.length ; i++)
		{
			let sel = mesAtual == i ? ' selected' : '';
		
			calendario = calendario + "<option value='" + meses[i] + "'" + sel + ">"+ meses[i] + "</option>";
		}
		calendario = calendario + "</select>";
        
        calendario = calendario + "<select style=\"float: right;\">";
		for (let a = 1970 ; a <= anoAtual ; a++)
		{
			let sel = anoAtual == a ? ' selected' : '';
		
			calendario = calendario + "<option value='" + a + "'" + sel + ">"+ a + "</option>";
		}
		calendario = calendario + "</select>";
        calendario = calendario + "</td>";
        calendario = calendario + "<td>";
        calendario = calendario + "<button type='button'>>></button>";
        calendario = calendario + "</td>";
        calendario = calendario + "</tr>";
        
        let diasMes = diasMeses[mesAtual];
        
        if (mesAtual == 1) //Fev
        {
        	if ((anoAtual % 400 == 0) || ((anoAtual % 4 == 0) && (anoAtual % 100 != 0)))
            {
            	diasMes = 29;
            }
        }
        
        while (dia < diasMes)
        {
        	calendario = calendario + "<tr>";
        
        	if (dia == 0)
            {
            	for (let ds = 0 ; ds < diasSemana.length ; ds++)
				{
					calendario = calendario + "<th>" + diasSemana[ds] + "</th>";
				}
                
                dia = 1;
            }
            else
			{
				for (let d = 0 ; d <= 6 ; d++)
				{
					let strDia = dia < 10 ? '0' + dia : dia;
					
					let dataCal = new Date(anoAtual, mesAtual, dia);
                    alert(strDia);
					
					if ((dataCal.getDay() != d) || (dataCal.getMonth() != mesAtual))
					{
						strDia = '';
					}
					else
					{
						dia = dia + 1;
					}
					
					calendario = calendario + "<td>" + strDia + "</td>";
				}
			}

			calendario = calendario + "</tr>";
        }

		calendario = calendario + "</table>";
		calendario = calendario + "</div>";

		calendario = calendario + "</div>";

		document.getElementById('pagina').innerHTML = calendario;
	}
	catch(err)
	{
		alert(err.message);
	}
}