import React, {useEffect, useState} from 'react';
import {View, ScrollView, StyleSheet} from 'react-native';
import {Card, Title, Paragraph, Button} from 'react-native-paper';
import {useAppDispatch} from '../store/hooks';
import {logoutThunk} from '../store/authSlice';
import {getProductos, getRutas, getMovimientos} from '../services/api';

export default function HomeScreen() {
  const dispatch = useAppDispatch();
  const [stats, setStats] = useState({
    totalProductos: 0,
    stockBajo: 0,
    rutasActivas: 0,
    movimientos: 0,
  });

  useEffect(() => {
    const load = async () => {
      try {
        const [prodRes, rutaRes, movRes] = await Promise.all([
          getProductos({per_page: 1000}),
          getRutas({estado: 'en_progreso'}),
          getMovimientos({per_page: 1000}),
        ]);
        const productos = prodRes.data.data;
        setStats({
          totalProductos: prodRes.data.meta.total,
          stockBajo: productos.filter(p => p.stock_actual <= p.stock_minimo).length,
          rutasActivas: rutaRes.data.meta.total,
          movimientos: movRes.data.meta.total,
        });
      } catch {}
    };
    load();
  }, []);

  return (
    <ScrollView style={styles.container}>
      <Title style={styles.title}>Dashboard</Title>
      <View style={styles.grid}>
        <Card style={styles.card}>
          <Card.Content>
            <Paragraph>Total Productos</Paragraph>
            <Title>{stats.totalProductos}</Title>
          </Card.Content>
        </Card>
        <Card style={styles.card}>
          <Card.Content>
            <Paragraph>Stock Bajo</Paragraph>
            <Title style={{color: 'red'}}>{stats.stockBajo}</Title>
          </Card.Content>
        </Card>
        <Card style={styles.card}>
          <Card.Content>
            <Paragraph>Rutas Activas</Paragraph>
            <Title style={{color: 'green'}}>{stats.rutasActivas}</Title>
          </Card.Content>
        </Card>
        <Card style={styles.card}>
          <Card.Content>
            <Paragraph>Movimientos</Paragraph>
            <Title>{stats.movimientos}</Title>
          </Card.Content>
        </Card>
      </View>
      <Button
        mode="outlined"
        onPress={() => dispatch(logoutThunk())}
        style={styles.logout}>
        Cerrar Sesión
      </Button>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, padding: 16, backgroundColor: '#f5f5f5'},
  title: {marginBottom: 16, fontSize: 24},
  grid: {flexDirection: 'row', flexWrap: 'wrap', gap: 12},
  card: {width: '47%', marginBottom: 8},
  logout: {marginTop: 24},
});
