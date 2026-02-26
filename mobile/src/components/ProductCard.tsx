import React from 'react';
import {StyleSheet} from 'react-native';
import {Card, Paragraph, Title, Badge} from 'react-native-paper';
import type {Producto} from '../types';

type Props = {
  producto: Producto;
  onPress?: () => void;
};

export default function ProductCard({producto, onPress}: Props) {
  const isLowStock = producto.stock_actual <= producto.stock_minimo;

  return (
    <Card style={styles.card} onPress={onPress}>
      <Card.Content>
        <Title>{producto.nombre}</Title>
        <Paragraph>SKU: {producto.sku}</Paragraph>
        <Paragraph>
          Stock:{' '}
          <Badge
            size={20}
            style={{backgroundColor: isLowStock ? 'red' : 'green'}}>
            {producto.stock_actual}
          </Badge>
        </Paragraph>
        {producto.precio && (
          <Paragraph>${Number(producto.precio).toFixed(2)}</Paragraph>
        )}
      </Card.Content>
    </Card>
  );
}

const styles = StyleSheet.create({
  card: {marginBottom: 12},
});
