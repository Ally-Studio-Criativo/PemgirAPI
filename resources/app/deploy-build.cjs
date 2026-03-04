const fs = require('fs')
const path = require('path')

const distDir = path.resolve(__dirname, 'dist/spa')
const publicDir = path.resolve(__dirname, '../../public')

// Arquivos/pastas do Laravel que NÃO devem ser sobrescritos
const protectedItems = [
  'index.php',
  '.htaccess',
  'robots.txt',
  'storage',  // Link simbólico
  'images',   // Imagens dinâmicas da landing page (não sobrescrever!)
]

function copyRecursiveSync(src, dest) {
  const exists = fs.existsSync(src)
  const stats = exists && fs.statSync(src)
  const isDirectory = exists && stats.isDirectory()

  if (isDirectory) {
    // Criar diretório se não existir
    if (!fs.existsSync(dest)) {
      fs.mkdirSync(dest, { recursive: true })
    }

    // Copiar conteúdo do diretório
    fs.readdirSync(src).forEach(childItemName => {
      copyRecursiveSync(
        path.join(src, childItemName),
        path.join(dest, childItemName)
      )
    })
  } else {
    // Copiar arquivo
    fs.copyFileSync(src, dest)
  }
}

function deploy() {
  if (!fs.existsSync(distDir)) {
    console.error('❌ Diretório dist/spa não encontrado. Execute "npm run build" primeiro.')
    process.exit(1)
  }

  console.log('📦 Iniciando deploy do build para public/...')

  // Listar arquivos no dist/spa
  const items = fs.readdirSync(distDir)

  items.forEach(item => {
    const srcPath = path.join(distDir, item)
    const destPath = path.join(publicDir, item)

    // Verificar se é um item protegido
    if (protectedItems.includes(item)) {
      console.log(`⏭️  Pulando (protegido): ${item}`)
      return
    }

    // Se já existe, remover primeiro (exceto itens protegidos)
    if (fs.existsSync(destPath)) {
      const stats = fs.statSync(destPath)
      if (stats.isDirectory()) {
        fs.rmSync(destPath, { recursive: true, force: true })
      } else {
        fs.unlinkSync(destPath)
      }
    }

    // Copiar item
    copyRecursiveSync(srcPath, destPath)
    console.log(`✓ Copiado: ${item}`)
  })

  console.log('✅ Deploy concluído! Frontend atualizado em public/')
}

deploy()
