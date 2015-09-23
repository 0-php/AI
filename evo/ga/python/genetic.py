# -*- coding: utf-8 -*-
import random

a = 0
b = 10
nm = 6

class ga:
    def F(self, xi):
        """Функция"""
        self.xi = xi
        yz = 5 - 24*xi + 17*(xi**2) - (11*(xi**3))/3 + (xi**4)/4
        return yz
    
    def dec(self, m):
        """Перевод в десятичную систему исчесления"""
        self.m = m
        qm = int(m)
        qb = '0b' + str(qm)
        res = int(qb,2)
        return float(res)

    def bi(self, x):
        """Представление числа в двоичном коде с нужным количеством символов"""
        self.x = x
        p = bin(x)[2:]
        while len(p) < len(bin(b)[2:]):
            p = '0' + p
        return p
    
    def pop(self):
        """Формирование популяции"""
        self.n = []
        for j in range(nm):
            i = random.randint(a,b)
            self.n.append(self.bi(i))
        return self.n
    
    def pair(self, pred):
        """Формирование брачных пар"""
        self.pred = pred      #родители
        self.pr = [[],[]]
        for i in range(len(pred)):
            self.pr[0].append(pred[i])
        for j in range(len(pred)):
            o = random.randint(0,len(bin(b)[2:]))
            self.pr[1].append(pred[o])
        i = 0
        while i < len(self.pr[0]):
            if self.pr[0][i] == self.pr[1][i]: #Одинаковые между собой особи не вступают в пары
                for j in range(len(self.pr)):
                    del self.pr[j][i]
            else:
                i += 1
        return self.pr
    
    def brkpnt(self, y):
        """Определение точек разрыва"""
        self.y = y
        self.zh = [[],[]]
        for i in range(len(y[0])):
                       d = random.randint(1,2)#Определяем положение точки разрыва
                       nv = len(bin(b)[2:])
                       """До точки разрыва"""
                       for j in range(len(y)):
                           self.zh[0].append(y[j][i][:d])
                       """После точки разрыва""" 
                       for k in range(len(y)):
                           self.zh[1].append(y[k][i][d:nv])        
        return self.zh
    
    def cld(self, q):
        """Рождение потомков"""
        self.q = q
        self.ch = []
        i = 0
        j = 0
        while i < len(self.q[0]):
            if i % 2 == 0:
                self.ch.append(self.q[j][i]+self.q[j+1][i+1])
            elif i % 2 == 1:
                self.ch.append(self.q[j][i]+self.q[j+1][i-1])
            i += 1
        return self.ch
    
    def mutation(self, w):
        """Мутация"""
        mp = 0.3 #Вероятность мутации
        self.w = w
        self.r = []
        """Разбиваем каждого потомка на отдельные части"""
        #Присваивание для элемента строки невозможно
        for m in range(len(w)):
            self.r.append([])
        for i in range(len(w)):
            for j in range(len(w[0])):
                self.r[i].append(w[i][j])
        """Мутируем потомков"""
        for k in range(len(w)):
            mpi = random.random()
            mpg = random.randint(0,2)#Случайным образом выбранный ген"
            if mpi <= mp:
                if self.r[k][mpg] == '1':
                    self.r[k][mpg] = '0'
                    self.r[k] = ''.join(self.r[k])
                elif self.r[k][mpg] == '0':
                    self.r[k][mpg] = '1'
                    self.r[k] = ''.join(self.r[k])
            else:
                self.r[k] = ''.join(self.r[k])

        return self.r

    def newgen(self, tm, tp):
        """Новая популяция"""
        self.tm = tm
        self.tp = tp
        self.ng = tm + tp #Изначально новая популяция состоит из мутантов и родителей. Далее естественный отбор...
        self.nnb = []
        #Идет естественный отбор, в новую популяцию войдут тольо лучшие особи из мутантов и родителей
        while len(self.nnb) < nm:
            im = 0
            mnn = self.F(self.dec(self.ng[im]))
            for i in range(1,len(self.ng)):
                if self.F(self.dec(self.ng[i])) < mnn:
                    mnn = self.F(self.dec(self.ng[i]))
                    im = i

            self.nnb.append(self.ng[im])

            nx = self.ng[im]

            j = 0
            while j < len(self.ng):
                try:
                    self.ng.remove(nx)
                except:
                    pass
                j += 1
        
        return self.nnb
                    
u = ga()

"""Формируем начальную популяцию"""

pred1 = u.pop()

ik = 0

while ik < 15:
  
    """Брачные пары"""

    wpairs = u.pair(pred1)

    """Точки разрыва"""
    predpnts = u.brkpnt(wpairs)

    """Потомки"""
    children = u.cld(predpnts)

    """Мутанты"""
    muts = u.mutation(children)

    """Новое поколение"""

    ngen = u.newgen(muts, pred1)
    pred1 = ngen
    ik += 1

xmin = u.dec(pred1[0])
minimum = u.F(xmin)

print u"Минимум функции равен %.2f" % minimum, u"в точке", xmin
