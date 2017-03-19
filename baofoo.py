#!/usr/local/bin/python
# -*- coding: utf-8 -*-
#
import tornado.ioloop
import subprocess
import json
import tornado.web


class Handler(tornado.web.RequestHandler):
    def get(self):
        try:
          content = str(self.get_argument('content'))
        except:
            return self.finish({'data': '参数错误', 'code': 500})

        proc = subprocess.Popen("php decrypt.php " + content, shell=True, stdout=subprocess.PIPE)
        script_response = proc.stdout.read()
        return self.finish(json.loads(script_response))

    def post(self):
        try:
            user_id = str(self.get_argument('user_id'))  # 平台USER_ID
            id_card = str(self.get_argument('id_card'))  # 身份证号码
            id_holder = str(self.get_argument('id_holder'))  # 姓名
            txn_amt = str(self.get_argument('txn_amt'))  # 交易金额额
        except:
            return self.finish({'data': '参数错误', 'code': 500})

        proc = subprocess.Popen("php encrypt.php " + user_id + " " + id_card + " " + id_holder + " " + txn_amt, shell=True,
                                stdout=subprocess.PIPE)
        script_response = proc.stdout.read()
        return self.finish(json.loads(script_response))


settings = {
    'template_path': 'views',
}

application = tornado.web.Application([
    (r"/baofoo", Handler),
], **settings)

if __name__ == "__main__":
    application.listen(8090)
    tornado.ioloop.IOLoop.instance().start()


