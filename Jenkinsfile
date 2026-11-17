pipeline {
    agent any

    environment {
        REMOTE_USER = 'itwekala-clms'
        REMOTE_PATH = '/home/itwekala-clms/htdocs/clms.itwekala.com.lk'
    }

    stages {
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('PHP Syntax Check') {
            steps {
                sh '''
                    echo "Checking PHP files for syntax errors..."
                    find . -name "*.php" -not -path "./vendor/*" -exec php -l {} +
                '''
            }
        }

        stage('Deploy to CloudPanel') {
            steps {
                withCredentials([string(credentialsId: 'cloudpanel-server-ip', variable: 'SERVER_IP')]) {
                    sshagent(credentials: ['cloudpanel-clms-ssh']) {
                        sh '''
                            rsync -avz --delete \
                            --exclude='.git/' \
                            --exclude='.gitignore' \
                            --exclude='.env' \
                            --exclude='uploads/' \
                            --exclude='*.rar' \
                            --exclude='*.zip' \
                            --exclude='Jenkinsfile' \
                            -e "ssh -o StrictHostKeyChecking=no" \
                            ./ ${REMOTE_USER}@${SERVER_IP}:${REMOTE_PATH}/
                        '''
                    }
                }
            }
        }

        stage('Set Permissions & Verify') {
            steps {
                withCredentials([string(credentialsId: 'cloudpanel-server-ip', variable: 'SERVER_IP')]) {
                    sshagent(credentials: ['cloudpanel-clms-ssh']) {
                        sh '''
                            ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${SERVER_IP} "
                                find ${REMOTE_PATH} -type d -exec chmod 770 {} + &&
                                find ${REMOTE_PATH} -type f -not -name '.env' -exec chmod 660 {} + &&
                                chmod 600 ${REMOTE_PATH}/.env
                            "
                        '''
                    }
                }
            }
        }
    }

    post {
        success {
            echo 'Deployment to https://clms.itwekala.com.lk succeeded!'
        }
        failure {
            echo 'Deployment failed. Check the console output above.'
        }
    }
}
