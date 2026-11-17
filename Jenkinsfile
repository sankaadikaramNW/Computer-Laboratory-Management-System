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

        stage('Deploy to CloudPanel') {
            steps {
                withCredentials([
                    string(credentialsId: 'cloudpanel-server-ip', variable: 'SERVER_IP'),
                    sshUserPrivateKey(credentialsId: 'cloudpanel-clms-ssh', keyFileVariable: 'SSH_KEY')
                ]) {
                    sh '''
                        tar --exclude='.git' \
                            --exclude='.gitignore' \
                            --exclude='.env' \
                            --exclude='uploads' \
                            --exclude='*.rar' \
                            --exclude='*.zip' \
                            --exclude='Jenkinsfile' \
                            -czf - . | ssh -i "$SSH_KEY" -o StrictHostKeyChecking=no ${REMOTE_USER}@${SERVER_IP} "tar -xzf - -C ${REMOTE_PATH}"
                    '''
                }
            }
        }

        stage('Verify PHP & Set Permissions') {
            steps {
                withCredentials([
                    string(credentialsId: 'cloudpanel-server-ip', variable: 'SERVER_IP'),
                    sshUserPrivateKey(credentialsId: 'cloudpanel-clms-ssh', keyFileVariable: 'SSH_KEY')
                ]) {
                    sh '''
                        ssh -i "$SSH_KEY" -o StrictHostKeyChecking=no ${REMOTE_USER}@${SERVER_IP} "
                            find ${REMOTE_PATH} -name '*.php' -exec php -l {} + &&
                            find ${REMOTE_PATH} -type d -exec chmod 770 {} + &&
                            find ${REMOTE_PATH} -type f -not -name '.env' -exec chmod 660 {} + &&
                            chmod 600 ${REMOTE_PATH}/.env
                        "
                    '''
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
