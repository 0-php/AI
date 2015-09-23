# -*- coding: utf-8 -*-
def y(x):
    y = float(5 - 24*x + 17*x**2 - 11/3*x**3 + 1/4*x**4)
    return float(y)

def bi(x):
    """Добавление нулей"""
    a = bin(x)
    y = a[2:]
    z = ''
    if len(y) == 2:
        z = '0' + y
    elif len(y) == 1:
        z = '00' + y
    else:
        z = y
    return z

def dim(x,m,n):
    """Создание матрицы, m - число столбцов, n - число строк"""
    for i in range(0,m):
        x.append([])
    for i in range(0,n):
        x[0].append(i+1)

def dec(m):
    """Перевод в десятичную систему исчесления"""
    q = int(m)
    qb = '0b' + str(q)
    res = int(qb,2)
    return float(res)

def strtodim(cs):
    """Разбиение строки на символы"""
    c = []
    for i in range(3):
        c.append(cs[i])
    return c


import random

n = 4
a = 0
b = 7

x = []

dim(x, 2, 4)

for i in range(0,n):
    x[1].append(random.randint(a,b))

print "Случайные значения аргумента функции"
for i in range(4):
    print x[0][i],'|',x[1][i], '|', y(x[1][i])

bx = []

dim(bx, 2, 4)

print "Особи"

for i in range(n):
    bx[1].append(bi(x[1][i]))


for i in range(4):
    print bx[0][i],'|',bx[1][i], '|', y(dec(bx[1][i]))

"""Выбираем пару"""
bp = []

dim(bp, 3, 4)


for i in range(n):
    bp[1].append(bx[1][i])
    z = random.randint(0,3)
    bp[2].append(bx[1][z])

print 'Брачные пары'
for i in range(n):
    print bp[0][i],'|',bp[1][i],'|',bp[2][i]

"""Одноточечный кроссинговер"""
"""Выбираем точку кроссинговера"""

i = 0
while i < len(bp[0]):
    if bp[1][i] == bp[2][i]: #Если брачные особи одинаковые, то не участвуют в размножении
        for j in range(3):
            del bp[j][i]
    else:
        i += 1
        
vc = len(bp[0])

print 'Брачные пары, участвующие в размножении'

for i in range(vc):
    print bp[0][i],'|',bp[1][i],'|',bp[2][i]

z = []

dim(z, 5, vc)

for i in range(vc):
    d = random.randint(1,2)
    """До точки разрыва первой особи"""
    z[1].append(bp[1][i][:d])
    """После точки разрыва первой особи"""
    z[2].append(bp[1][i][d:3])
    """До точки разрыва второй особи"""
    z[3].append(bp[2][i][:d])
    """После точки разрыва второй особи"""
    z[4].append(bp[2][i][d:3])
    
print "Разрывные точки"
for i in range(vc):
    print z[0][i],'|',z[1][i],'|',z[2][i],'|',z[3][i],'|',z[4][i]
"""Потомки"""
print "Потомки"
child = []
dim(child, 3, vc)

for i in range(0,vc):
    child[1].append(z[1][i] + z[4][i])
    child[2].append(z[3][i] + z[2][i])
for i in range(vc):
    print child[0][i],'|',child[1][i],'|',child[2][i],'|', y(dec(child[1][i])),'|', y(dec(child[2][i]))


"""Мутация"""
mchild = []
dim(mchild, 3, vc) #Матрица мутантов

for i in range(vc):
    for j in range(1,3):
        mchild[j].append(child[j][i]) #Вначале мутанты такие же как и потомки

mp = 0.3 #Вероятность мутации


"""Мутация 1ых потомков"""
for i in range(vc):
    mpi = random.random()
    mpg = random.randint(0,2)#Случайным образом выбранный ген"
    if mpi <= mp:
        if child[1][i][mpg] == '1':
            mb = strtodim(child[1][i])
            mb[mpg] = '0'
            mchild[1][i] = ''.join(mb)
        elif child[1][i][mpg] == '0':
            mb = strtodim(child[1][0])
            mb[mpg] = '1'
            mchild[1][i] = ''.join(mb)

"""Мутация 2ых потомков"""
for i in range(vc):
    mpi = random.random()
    mpg = random.randint(0,2)#Случайным образом выбранный ген"
    if mpi <= mp:
        if child[2][i][mpg] == '1':
            mb = strtodim(child[2][i])
            mb[mpg] = '0'
            mchild[2][i] = ''.join(mb)
        elif child[2][i][mpg] == '0':
            mb = strtodim(child[2][0])
            mb[mpg] = '1'
            mchild[2][i] = ''.join(mb)

print "Мутанты"
for i in range(0,vc):
    print mchild[0][i],'|',mchild[1][i],'|',mchild[2][i],'|',y(dec(mchild[1][i])),'|',y(dec(mchild[2][i]))        

"""Новая популяция"""
print "Особи, участвующие в отборе"

nb = []

"""Добавляем в новую популяцию родителей"""
for i in range(1, 2):
    for j in range(0, len(bx[0])):
        nb.append(bx[i][j])
"""Добавляем в новую популяцию мутантов-потомков"""
for i in range(1, 3):
    for j in range(vc):
        nb.append(mchild[i][j])

for i in range(len(nb)):
    print nb[i], '|', y(dec(nb[i]))

nnb = []
while len(nnb) < 4:
    mn = y(dec(nb[0]))
    for i in range(1,len(nb)):
        if y(dec(nb[i])) < mn:
            mn = y(dec(nb[i]))
            im = i


    nnb.append(nb[im])
    nx = nb[im]

    j = 0
    while j < len(nb):
        try:
            nb.remove(nx)
        except:
            pass
        j += 1

print 
for i in range(len(nnb)):
    print nnb[i]





    
