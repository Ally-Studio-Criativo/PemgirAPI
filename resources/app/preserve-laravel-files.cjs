const fs = require('fs')
const path = require('path')

const publicDir = path.resolve(__dirname, '../../public')
const filesToPreserve = [
  'index.php',
  '.htaccess',
  'robots.txt'
]

const linksToPreserve = ['storage']

const backupDir = path.resolve(__dirname, '.laravel-backup')

function backup() {
  if (!fs.existsSync(backupDir)) {
    fs.mkdirSync(backupDir, { recursive: true })
  }

  filesToPreserve.forEach(file => {
    const source = path.join(publicDir, file)
    const dest = path.join(backupDir, file)

    if (fs.existsSync(source)) {
      fs.copyFileSync(source, dest)
      console.log(`✓ Backed up: ${file}`)
    }
  })

  // Backup symbolic links
  linksToPreserve.forEach(link => {
    const linkPath = path.join(publicDir, link)
    const linkInfoPath = path.join(backupDir, `${link}.link`)

    if (fs.existsSync(linkPath) && fs.lstatSync(linkPath).isSymbolicLink()) {
      const target = fs.readlinkSync(linkPath)
      fs.writeFileSync(linkInfoPath, target)
      console.log(`✓ Backed up symlink: ${link} -> ${target}`)
    }
  })
}

function restore() {
  if (!fs.existsSync(backupDir)) {
    console.log('No backup directory found')
    return
  }

  filesToPreserve.forEach(file => {
    const source = path.join(backupDir, file)
    const dest = path.join(publicDir, file)

    if (fs.existsSync(source)) {
      fs.copyFileSync(source, dest)
      console.log(`✓ Restored: ${file}`)
    }
  })

  // Restore symbolic links
  linksToPreserve.forEach(link => {
    const linkPath = path.join(publicDir, link)
    const linkInfoPath = path.join(backupDir, `${link}.link`)

    if (fs.existsSync(linkInfoPath)) {
      const target = fs.readFileSync(linkInfoPath, 'utf8')

      // Remove existing link/directory if exists
      if (fs.existsSync(linkPath)) {
        fs.rmSync(linkPath, { recursive: true, force: true })
      }

      // Create symbolic link
      fs.symlinkSync(target, linkPath)
      console.log(`✓ Restored symlink: ${link} -> ${target}`)
    }
  })
}

const action = process.argv[2]

if (action === 'backup') {
  backup()
} else if (action === 'restore') {
  restore()
} else {
  console.log('Usage: node preserve-laravel-files.js [backup|restore]')
}
