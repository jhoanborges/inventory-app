import React, {useEffect} from 'react';
import {View, FlatList, StyleSheet} from 'react-native';
import {Card, Title, Paragraph, Badge} from 'react-native-paper';
import {useAppDispatch, useAppSelector} from '../store/hooks';
import {fetchRutas} from '../store/rutasSlice';
import type {NativeStackNavigationProp} from '@react-navigation/native-stack';

type Props = {
  navigation: NativeStackNavigationProp<any>;
};

const estadoColor = (estado: string) => {
  switch (estado) {
    case 'pendiente':
      return '#ffa500';
    case 'en_progreso':
      return '#2196f3';
    case 'completada':
      return '#4caf50';
    default:
      return '#999';
  }
};

const estadoLabel = (estado: string) => {
  switch (estado) {
    case 'pendiente':
      return 'Pendiente';
    case 'en_progreso':
      return 'En Progreso';
    case 'completada':
      return 'Completada';
    default:
      return estado;
  }
};

export default function RutasScreen({navigation}: Props) {
  const dispatch = useAppDispatch();
  const {items, loading} = useAppSelector(s => s.rutas);

  useEffect(() => {
    dispatch(fetchRutas({}));
  }, [dispatch]);

  return (
    <View style={styles.container}>
      <FlatList
        data={items}
        keyExtractor={item => String(item.id)}
        refreshing={loading}
        onRefresh={() => dispatch(fetchRutas({}))}
        renderItem={({item}) => (
          <Card
            style={styles.card}
            onPress={() => navigation.navigate('RutaDetail', {ruta: item})}>
            <Card.Content>
              <View style={styles.row}>
                <Title style={styles.cardTitle}>{item.nombre}</Title>
                <Badge style={{backgroundColor: estadoColor(String(item.estado))}}>
                  {estadoLabel(String(item.estado))}
                </Badge>
              </View>
              <Paragraph>
                {item.origen} → {item.destino}
              </Paragraph>
              {item.vehiculo && <Paragraph>Vehículo: {item.vehiculo}</Paragraph>}
              {item.operador && (
                <Paragraph>Operador: {item.operador.name}</Paragraph>
              )}
            </Card.Content>
          </Card>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {flex: 1, padding: 16, backgroundColor: '#f5f5f5'},
  card: {marginBottom: 12},
  row: {flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center'},
  cardTitle: {fontSize: 18},
});
